<?php

namespace App\Services;

use App\Models\MedicalKnowledge;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class AiAssistantService
{
    public function buildClinicalContext(array $data, ?Patient $patient = null): array
    {
        return [
            'patient_name' => $patient ? $patient->first_name.' '.$patient->last_name : ($data['patient_name'] ?? 'Patient'),
            'age' => $patient ? $patient->date_of_birth?->age : null,
            'symptoms' => $data['symptoms'] ?? '',
            'history' => $data['history'] ?? '',
            'allergies' => $data['allergies'] ?? '',
            'medications' => $data['medications'] ?? '',
            'vitals' => $data['vitals'] ?? '',
            'exam' => $data['exam'] ?? '',
            'diagnostic_goal' => $data['diagnostic_goal'] ?? 'Aider à la décision clinique et à la prescription.',
        ];
    }

    public function retrieveKnowledge(array $context): Collection
    {
        $query = trim(implode(' ', [
            $context['symptoms'] ?? '',
            $context['history'] ?? '',
            $context['exam'] ?? '',
            $context['vitals'] ?? '',
            $context['diagnostic_goal'] ?? '',
        ]));

        $table = (new MedicalKnowledge)->getTable();

        if ($query !== '' && Schema::hasColumn($table, 'embedding')) {
            $embedding = $this->embedText($query);

            if (is_array($embedding) && $embedding !== []) {
                return MedicalKnowledge::query()
                    ->where('is_active', true)
                    ->selectVectorDistance('embedding', $embedding, 'vector_similarity')
                    ->orderBy('vector_similarity')
                    ->limit(5)
                    ->get();
            }
        }

        $keywords = preg_split('/\s+/', Str::slug($query, ' '));
        $keywords = array_filter($keywords, fn ($word) => strlen($word) > 2);

        $builder = MedicalKnowledge::query()->where('is_active', true);

        if ($keywords !== []) {
            foreach ($keywords as $keyword) {
                $builder->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('content', 'like', "%{$keyword}%")
                        ->orWhereJsonContains('tags', $keyword);
                });
            }

            return $builder->limit(8)->get();
        }

        return $builder->limit(5)->get();
    }

    public function generateRecommendation(array $context, Collection $knowledge): array
    {
        $knowledgeText = $knowledge
            ->pluck('content')
            ->filter(fn ($content) => is_string($content) || is_numeric($content))
            ->map(fn ($content) => trim((string) $content))
            ->filter()
            ->implode("\n\n");

        if ($knowledgeText === '') {
            $knowledgeText = 'Aucune connaissance structurée disponible. Prioriser l’évaluation clinique, la sécurité du patient et les données de suivi.';
        }

        $provider = config('services.ai.provider', 'openai');

        if ($provider === 'ollama') {
            return $this->generateWithOllama($context, $knowledge, $knowledgeText);
        }

        if ($provider !== 'openai') {
            throw new \RuntimeException('Fournisseur IA non pris en charge : '.$provider);
        }

        if (filled(config('services.openai.api_key'))) {
            $payload = [
                'model' => config('services.openai.chat_model', 'gpt-4o-mini'),
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un assistant médical d’aide à la décision. Tu ne remplaces pas le médecin. Réponds toujours de manière prudente, concise et structurée, en JSON valide.',
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Contexte patient : '.json_encode($context, JSON_THROW_ON_ERROR)."\n\nConnaissances pertinentes : ".$knowledgeText,
                    ],
                ],
            ];

            try {
                $response = Http::withToken(config('services.openai.api_key'))
                    ->acceptJson()
                    ->post('https://api.openai.com/v1/chat/completions', $payload);

                if ($response->failed()) {
                    throw new \RuntimeException('OpenAI request failed.');
                }

                $content = $response->json('choices.0.message.content');
                $parsed = $this->parseJsonResponse($content);

                if (is_array($parsed) && $parsed !== []) {
                    return $this->normalizeRecommendation($parsed, $context, $knowledgeText);
                }
            } catch (Throwable $e) {
                // Fallback sécurisé en cas d’échec serveur ou d’absence de clé.
            }
        }

        $diagnostic = $this->extractDiagnosticFromContext($context, $knowledgeText);

        return [
            'diagnostic' => $diagnostic['diagnostic'] ?? 'Diagnostic clinique à confirmer par le médecin.',
            'traitement' => $diagnostic['traitement'] ?? 'Traitement à adapter selon l’examen clinique final.',
            'medicaments' => $diagnostic['medicaments'] ?? ['Médicament principal à valider par le prescripteur'],
            'medicaments_suivi' => $diagnostic['medicaments_suivi'] ?? ['Suivi symptomatique et surveillance clinique'],
            'suivi' => $diagnostic['suivi'] ?? 'Contrôler la réponse thérapeutique dans 48 à 72 heures et revoir selon évolution.',
            'precautions' => $diagnostic['precautions'] ?? 'Vérifier les allergies, interactions médicamenteuses et contre-indications avant prescription finale.',
            'sources' => $knowledge->take(5)->pluck('title')->filter()->values()->all(),
            'raw_prompt' => $knowledgeText,
        ];
    }

    protected function generateWithOllama(array $context, Collection $knowledge, string $knowledgeText): array
    {
        $baseUrl = rtrim((string) config('services.ai.ollama.url'), '/');
        $model = (string) config('services.ai.ollama.model');

        if ($baseUrl === '' || $model === '') {
            throw new \RuntimeException('OLLAMA_URL et OLLAMA_MODEL doivent être configurés.');
        }

        $response = Http::acceptJson()
            ->connectTimeout(10)
            ->timeout((int) config('services.ai.ollama.timeout', 120))
            ->retry([500, 1500], 0, fn (Throwable $exception): bool => $exception instanceof ConnectionException
                || ($exception instanceof RequestException
                    && ($exception->response->serverError() || $exception->response->status() === 429)))
            ->post($baseUrl.'/api/chat', [
                'model' => $model,
                'stream' => false,
                'format' => 'json',
                'options' => [
                    'num_predict' => 512,
                ],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un assistant médical d’aide à la décision, pas un médecin. Ne pose pas de diagnostic définitif et ne prescris pas de médicament ni de dose. Signale les signes d’urgence. Réponds uniquement en français dans un objet JSON valide contenant exactement les clés diagnostic, traitement, medicaments (tableau de textes), medicaments_suivi (tableau de textes), suivi et precautions. Toutes les propositions doivent être vérifiées par un professionnel de santé.',
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Contexte patient : '.json_encode($context, JSON_THROW_ON_ERROR)."\n\nConnaissances pertinentes : ".$knowledgeText,
                    ],
                ],
            ]);

        $response->throw();

        $content = $response->json('message.content');
        $parsed = $this->parseJsonResponse(is_string($content) ? $content : null);

        if (! is_array($parsed) || $parsed === []) {
            throw new \RuntimeException('Ollama a renvoyé une réponse vide ou invalide.');
        }

        return $this->normalizeRecommendation($parsed, $context, $knowledgeText);
    }

    public function seedKnowledgeBase(): void
    {
        $entries = [
            [
                'title' => 'Fièvre et malaise général',
                'category' => 'urgentiste',
                'content' => 'Toute fièvre avec altération de l’état général, douleurs thoraciques, dyspnée importante, confusion ou hémorragie nécessite une évaluation médicale urgente. L’examen clinique et les paramètres vitaux orientent le diagnostic et les soins.',
                'tags' => ['fièvre', 'malaise', 'urgence'],
                'source' => 'Protocoles cliniques de référence',
            ],
            [
                'title' => 'Hypertension artérielle - prise en charge',
                'category' => 'cardiologie',
                'content' => 'L’hypertension artérielle doit être évaluée en fonction des chiffres tensionnels, de l’âge du patient, des antécédents et des symptômes associés. La surveillance régulière et l’adaptation thérapeutique sont essentielles.',
                'tags' => ['hypertension', 'cardio', 'tension'],
                'source' => 'Guidelines de prise en charge',
            ],
            [
                'title' => 'Douleur abdominale aiguë',
                'category' => 'gastro',
                'content' => 'Une douleur abdominale aiguë ou d’intensité croissante nécessite une évaluation rapide, notamment si elle s’accompagne de fièvre, de vomissements, de défense ou de troubles hémodynamiques.',
                'tags' => ['abdomen', 'douleur', 'urgence'],
                'source' => 'Protocoles de triage',
            ],
            [
                'title' => 'Infection respiratoire',
                'category' => 'pneumologie',
                'content' => 'Les infections respiratoires doivent être évaluées par l’examen clinique, la saturation, la température et la présence de signes de gravité. Le traitement dépend du contexte infectieux et des antécédents du patient.',
                'tags' => ['respiration', 'toux', 'infection'],
                'source' => 'Recommandations de santé publique',
            ],
            [
                'title' => 'Allergies aux médicaments',
                'category' => 'sécurité',
                'content' => 'Avant toute prescription, vérifier les allergies connues, les contre-indications et les interactions entre médicaments. En cas de réaction allergique, interrompre le traitement et évaluer le patient.',
                'tags' => ['allergie', 'médicament', 'sécurité'],
                'source' => 'Bonne pratique de prescription',
            ],
        ];

        foreach ($entries as $entry) {
            $payload = [
                'title' => $entry['title'],
                'category' => $entry['category'],
                'content' => $entry['content'],
                'tags' => $entry['tags'],
                'source' => $entry['source'],
                'is_active' => true,
            ];

            if (Schema::hasColumn((new MedicalKnowledge)->getTable(), 'embedding')) {
                $embedding = $this->embedText($entry['title'].' '.$entry['content']);
                if (is_array($embedding) && $embedding !== []) {
                    $payload['embedding'] = json_encode($embedding);
                }
            }

            MedicalKnowledge::query()->updateOrCreate(
                ['title' => $entry['title']],
                $payload,
            );
        }
    }

    protected function embedText(string $text): ?array
    {
        $apiKey = config('services.openai.api_key');

        if (blank($apiKey)) {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->post('https://api.openai.com/v1/embeddings', [
                    'model' => config('services.openai.embedding_model', 'text-embedding-3-small'),
                    'input' => Str::limit(trim($text), 4000),
                ]);

            $embedding = $response->json('data.0.embedding');

            return is_array($embedding) ? $embedding : null;
        } catch (Throwable) {
            return null;
        }
    }

    protected function parseJsonResponse(?string $content): ?array
    {
        if (blank($content)) {
            return null;
        }

        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : null;
    }

    protected function normalizeRecommendation(array $result, array $context, string $knowledgeText): array
    {
        $medications = $result['medicaments'] ?? $result['medications'] ?? $result['prescriptions'] ?? ['Aucun médicament proposé; validation clinique requise.'];
        $followUpMedications = $result['medicaments_suivi'] ?? $result['follow_up_medications'] ?? $result['medication_follow_up'] ?? ['Suivi à définir par le médecin.'];

        return [
            'diagnostic' => $result['diagnostic'] ?? $result['diagnosis'] ?? 'Diagnostic clinique à confirmer par le médecin.',
            'traitement' => $result['traitement'] ?? $result['treatment'] ?? 'Traitement adapté selon l’évaluation clinique.',
            'medicaments' => array_values((array) $medications),
            'medicaments_suivi' => array_values((array) $followUpMedications),
            'suivi' => $result['suivi'] ?? $result['follow_up'] ?? 'Contrôler la réponse thérapeutique dans 48 à 72 heures.',
            'precautions' => $result['precautions'] ?? $result['warnings'] ?? 'Vérifier les allergies et interactions avant toute décision clinique.',
            'sources' => $this->extractSources($knowledgeText),
            'raw_prompt' => $knowledgeText."\n\nContexte : ".json_encode($context, JSON_THROW_ON_ERROR),
        ];
    }

    protected function extractDiagnosticFromContext(array $context, string $knowledgeText): array
    {
        $symptoms = strtolower((string) ($context['symptoms'] ?? ''));
        $exam = strtolower((string) ($context['exam'] ?? ''));
        $vitals = strtolower((string) ($context['vitals'] ?? ''));

        $diagnostic = 'Diagnostic clinique à confirmer par le médecin.';
        $traitement = 'Traitement à adapter selon l’examen clinique final.';
        $medicaments = ['Médicament principal à valider par le prescripteur'];
        $medicaments_suivi = ['Suivi symptomatique et surveillance clinique'];
        $suivi = 'Contrôler la réponse thérapeutique dans 48 à 72 heures et revoir selon évolution.';
        $precautions = 'Vérifier les allergies, interactions médicamenteuses et contre-indications avant prescription finale.';

        if (str_contains($symptoms, 'fièvre') || str_contains($exam, 'fièvre') || str_contains($vitals, 'température')) {
            $diagnostic = 'Suspicion d’infection ou syndrome fébrile à confirmer selon l’examen clinique et les paramètres vitaux.';
            $traitement = 'Évaluation de la cause fébrile et traitement symptomatique ou étiologique selon les données cliniques.';
            $medicaments = ['Paracétamol si nécessaire et toléré'];
        }

        if (str_contains($symptoms, 'toux') || str_contains($symptoms, 'respiration') || str_contains($exam, 'respiratoire')) {
            $diagnostic = 'Syndrome respiratoire ou infection respiratoire à confirmer par l’examen clinique.';
            $traitement = 'Surveillance de la saturation, traitement symptomatique et évaluation de la gravité.';
            $medicaments = ['Traitement symptomatique adapté au contexte respiratoire'];
        }

        if (str_contains($symptoms, 'douleur') && (str_contains($symptoms, 'abdomen') || str_contains($exam, 'abdomen'))) {
            $diagnostic = 'Douleur abdominale à évaluer selon la localisation, l’intensité et les signes de gravité.';
            $traitement = 'Évaluation clinique et paraclinique adaptée en fonction du contexte, avec surveillance étroite.';
            $medicaments = ['Analgesie adaptée à la douleur et au contexte clinique'];
        }

        if (str_contains($knowledgeText, 'allergie')) {
            $precautions = 'Vérifier de manière systématique les allergies et les interactions médicamenteuses avant toute prescription.';
        }

        return compact('diagnostic', 'traitement', 'medicaments', 'medicaments_suivi', 'suivi', 'precautions');
    }

    protected function extractSources(string $knowledgeText): array
    {
        $sources = preg_split('/\n\n+/', $knowledgeText);

        return array_values(array_filter(array_map('trim', $sources ?: []), fn ($source) => $source !== ''));
    }
}
