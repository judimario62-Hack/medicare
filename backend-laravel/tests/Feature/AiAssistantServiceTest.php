<?php

namespace Tests\Feature;

use App\Models\AiRequest;
use App\Models\AiSummary;
use App\Models\User;
use App\Services\AiAssistantService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAssistantServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generated_result_page_displays_nested_model_values_without_crashing(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $request = AiRequest::query()->create([
            'requested_by' => $admin->id,
            'type' => 'clinical_assistant',
            'status' => 'completed',
            'input_data' => ['patient_name' => 'Test'],
            'attempts' => 1,
            'completed_at' => now(),
        ]);
        AiSummary::query()->create([
            'ai_request_id' => $request->id,
            'content' => json_encode([
                'diagnostic' => ['orientation' => 'À confirmer', 'note' => 'Examen nécessaire'],
                'traitement' => 'Évaluation clinique',
                'medicaments' => [['nom' => 'Médicament à valider', 'statut' => 'à vérifier']],
                'medicaments_suivi' => [['action' => 'Surveillance', 'détail' => 'selon le médecin']],
                'suivi' => 'Selon évolution',
                'precautions' => 'Vérifier les allergies',
            ], JSON_THROW_ON_ERROR),
            'sources' => [],
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->get(route('assistant.result', $request))
            ->assertOk()
            ->assertSee('À confirmer — Examen nécessaire')
            ->assertSee('Médicament à valider — à vérifier')
            ->assertSee('Surveillance — selon le médecin');
    }

    public function test_generation_stores_the_request_and_redirects_to_the_result(): void
    {
        config(['services.ai.provider' => 'ollama']);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $service = \Mockery::mock(AiAssistantService::class);
        $service->shouldReceive('buildClinicalContext')
            ->once()
            ->andReturn(['symptoms' => 'Toux de test', 'patient_name' => 'Patient']);
        $service->shouldReceive('retrieveKnowledge')
            ->once()
            ->andReturn(new Collection);
        $service->shouldReceive('generateRecommendation')
            ->once()
            ->andReturn([
                'diagnostic' => 'À confirmer par un médecin.',
                'traitement' => 'Évaluation clinique.',
                'medicaments' => [],
                'medicaments_suivi' => [],
                'suivi' => 'Selon évolution.',
                'precautions' => 'Vérifier les allergies.',
            ]);

        $this->app->instance(AiAssistantService::class, $service);

        $response = $this->actingAs($admin)
            ->post(route('assistant.generate'), [
                'patient_id' => null,
                'symptoms' => 'Toux de test',
            ]);

        $aiRequest = AiRequest::query()->firstOrFail();
        $response->assertRedirect(route('assistant.result', $aiRequest));
        $this->assertDatabaseHas('ai_summaries', [
            'ai_request_id' => $aiRequest->id,
            'status' => 'draft',
        ]);
        $this->assertSame(
            'À confirmer par un médecin.',
            json_decode(AiSummary::query()->firstOrFail()->content, true)['diagnostic'],
        );
    }

    public function test_it_uses_the_configured_ollama_model_for_recommendations(): void
    {
        Http::preventStrayRequests();
        config([
            'services.ai.provider' => 'ollama',
            'services.ai.ollama.url' => 'http://127.0.0.1:11434',
            'services.ai.ollama.model' => 'medgemma',
        ]);

        $sequence = Http::sequence()
            ->push(['error' => 'temporary model startup failure'], 503)
            ->push([
                'message' => [
                    'content' => json_encode([
                        'diagnosis' => 'À confirmer par un médecin.',
                        'treatment' => 'Évaluation clinique nécessaire.',
                        'medications' => [['name' => 'Option à valider']],
                        'follow_up_medications' => ['Surveillance clinique'],
                        'follow_up' => 'Réévaluer selon évolution.',
                        'precautions' => 'Vérifier les allergies.',
                    ], JSON_THROW_ON_ERROR),
                ],
            ]);

        Http::fake(['http://127.0.0.1:11434/api/chat' => $sequence]);

        $context = [
            'patient_name' => 'Synthetic patient',
            'age' => 40,
            'symptoms' => 'cough',
            'history' => 'synthetic history',
            'allergies' => 'none known',
            'medications' => 'none',
            'vitals' => 'temperature 37.2',
            'exam' => 'synthetic exam',
            'diagnostic_goal' => 'clinical evaluation',
        ];
        $result = app(AiAssistantService::class)->generateRecommendation(
            $context,
            new Collection,
        );

        $this->assertSame('À confirmer par un médecin.', $result['diagnostic']);
        $this->assertSame('Évaluation clinique nécessaire.', $result['traitement']);
        $this->assertSame([['name' => 'Option à valider']], $result['medicaments']);
        $this->assertSame(['Surveillance clinique'], $result['medicaments_suivi']);
        Http::assertSentCount(2);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'http://127.0.0.1:11434/api/chat'
            && $request['model'] === 'medgemma'
            && $request['stream'] === false
            && $request['format'] === 'json'
            && $request['options']['num_predict'] === 512
            && str_contains($request['messages'][1]['content'], 'Synthetic patient')
            && str_contains($request['messages'][1]['content'], 'synthetic history')
            && str_contains($request['messages'][1]['content'], 'clinical evaluation'));
    }

    public function test_it_surfaces_ollama_connection_errors_instead_of_using_demo_recommendations(): void
    {
        config(['services.ai.provider' => 'ollama']);
        Http::fake([
            '*/api/chat' => Http::response(['error' => 'model not found'], 404),
        ]);

        $this->expectException(RequestException::class);

        app(AiAssistantService::class)->generateRecommendation([], new Collection);
    }

    public function test_generation_returns_to_the_form_when_ollama_is_unavailable(): void
    {
        config(['services.ai.provider' => 'ollama']);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $service = \Mockery::mock(AiAssistantService::class);
        $service->shouldReceive('buildClinicalContext')->once()->andReturn([]);
        $service->shouldReceive('retrieveKnowledge')->once()->andReturn(new Collection);
        $service->shouldReceive('generateRecommendation')
            ->once()
            ->andThrow(new ConnectionException('Operation timed out'));

        $this->app->instance(AiAssistantService::class, $service);

        $this->actingAs($admin)
            ->from(route('assistant.index'))
            ->post(route('assistant.generate'), [
                'patient_id' => null,
                'symptoms' => 'Données synthétiques de test',
            ])
            ->assertRedirect(route('assistant.index'))
            ->assertSessionHasErrors([
                'assistant' => 'La génération de MedGemma a dépassé le délai prévu. Augmentez OLLAMA_TIMEOUT puis réessayez.',
            ])
            ->assertSessionHasInput('symptoms', 'Données synthétiques de test');
    }

    public function test_generation_explains_when_the_configured_ollama_model_is_missing(): void
    {
        config([
            'services.ai.provider' => 'ollama',
            'services.ai.ollama.url' => 'http://127.0.0.1:11434',
            'services.ai.ollama.model' => 'missing-model',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Http::preventStrayRequests();
        Http::fake([
            'http://127.0.0.1:11434/api/chat' => Http::response(['error' => 'model not found'], 404),
        ]);

        $this->actingAs($admin)
            ->from(route('assistant.index'))
            ->post(route('assistant.generate'), [
                'patient_id' => null,
                'symptoms' => 'Données synthétiques de test',
            ])
            ->assertRedirect(route('assistant.index'))
            ->assertSessionHasErrors([
                'assistant' => 'Le modèle configuré est introuvable. Vérifiez OLLAMA_MODEL et assurez-vous que le modèle est téléchargé.',
            ])
            ->assertSessionHasInput('symptoms', 'Données synthétiques de test');
    }
}
