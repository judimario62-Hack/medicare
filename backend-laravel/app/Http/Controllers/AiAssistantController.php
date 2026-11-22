<?php

namespace App\Http\Controllers;

use App\Models\AiRequest;
use App\Models\AiSummary;
use App\Models\Patient;
use App\Services\AiAssistantService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AiAssistantController extends Controller
{
    public function index(): View
    {
        $patients = Patient::query()->orderBy('last_name')->orderBy('first_name')->get();

        return view('assistant.index', compact('patients'));
    }

    public function generate(Request $request, AiAssistantService $service): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['nullable', 'exists:patients,id'],
            'patient_name' => ['nullable', 'string', 'max:255'],
            'symptoms' => ['required', 'string'],
            'history' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'vitals' => ['nullable', 'string'],
            'exam' => ['nullable', 'string'],
            'diagnostic_goal' => ['nullable', 'string'],
        ]);

        $patientId = $validated['patient_id'] ?? null;
        $patient = $patientId ? Patient::query()->findOrFail($patientId) : null;

        $context = $service->buildClinicalContext($validated, $patient);
        $knowledge = $service->retrieveKnowledge($context);

        if (config('services.ai.provider') === 'ollama') {
            $ollamaTimeout = (int) config('services.ai.ollama.timeout', 120);
            set_time_limit($ollamaTimeout + 30);

            try {
                $recommendation = $service->generateRecommendation($context, $knowledge);
            } catch (ConnectionException $exception) {
                $details = strtolower($exception->getMessage());
                $message = str_contains($details, 'timed out') || str_contains($details, 'timeout')
                    ? 'La génération de MedGemma a dépassé le délai prévu. Augmentez OLLAMA_TIMEOUT puis réessayez.'
                    : 'Laravel ne peut pas joindre Ollama. Vérifiez que le port 11434 est accessible.';

                Log::warning('Ollama connection failed during clinical assistant generation.', [
                    'exception' => $exception::class,
                    'timed_out' => str_contains($details, 'timed out') || str_contains($details, 'timeout'),
                ]);

                return back()->withInput()->withErrors([
                    'assistant' => $message,
                ]);
            } catch (RequestException $exception) {
                $status = $exception->response->status();
                $message = match ($status) {
                    404 => 'Le modèle configuré est introuvable. Vérifiez OLLAMA_MODEL et assurez-vous que le modèle est téléchargé.',
                    400 => 'Ollama a refusé la demande. Vérifiez la configuration du modèle et réessayez.',
                    default => $status >= 500 || $status === 429
                        ? 'Ollama est temporairement indisponible ou surchargé. Attendez quelques instants puis réessayez.'
                        : 'Ollama a refusé la demande. Vérifiez sa configuration puis réessayez.',
                };

                Log::warning('Ollama rejected clinical assistant generation.', [
                    'status' => $status,
                    'exception' => $exception::class,
                ]);

                return back()->withInput()->withErrors([
                    'assistant' => $message,
                ]);
            } catch (\JsonException|\RuntimeException $exception) {
                Log::error('Ollama returned an unusable clinical assistant response.', [
                    'exception' => $exception::class,
                    'message' => mb_substr($exception->getMessage(), 0, 300),
                ]);

                return back()->withInput()->withErrors([
                    'assistant' => 'MedGemma n’a pas renvoyé de réponse exploitable. Les champs saisis sont conservés; vous pouvez réessayer.',
                ]);
            }
        } else {
            $recommendation = $service->generateRecommendation($context, $knowledge);
        }

        $aiRequest = AiRequest::query()->create([
            'requested_by' => Auth::id(),
            'patient_id' => $patient?->id,
            'type' => 'clinical_assistant',
            'status' => 'completed',
            'input_data' => $context,
            'attempts' => 1,
            'completed_at' => now(),
        ]);

        AiSummary::query()->create([
            'ai_request_id' => $aiRequest->id,
            'patient_id' => $patient?->id,
            'content' => json_encode($recommendation, JSON_THROW_ON_ERROR),
            'sources' => $knowledge->take(5)->pluck('title')->all(),
            'status' => 'draft',
        ]);

        return redirect()->route('assistant.result', $aiRequest)->with('ai_result', $recommendation);
    }

    public function result(AiRequest $aiRequest): View
    {
        $aiRequest->load('patient', 'summary', 'requester');

        $result = $this->normalizeResult($aiRequest->summary?->content);

        return view('assistant.result', [
            'request' => $aiRequest,
            'result' => $result,
        ]);
    }

    public function prescriptionPdf(AiRequest $aiRequest)
    {
        $aiRequest->load('patient', 'summary', 'requester');

        $result = $this->normalizeResult($aiRequest->summary?->content);

        $doctorName = $aiRequest->requester ? trim(($aiRequest->requester->first_name ?? '').' '.($aiRequest->requester->last_name ?? '')) : 'Médecin';
        $patientName = $aiRequest->patient ? trim(($aiRequest->patient->first_name ?? '').' '.($aiRequest->patient->last_name ?? '')) : ($aiRequest->input_data['patient_name'] ?? 'Patient');

        $pdf = Pdf::loadView('assistant.prescription-pdf', [
            'result' => $result,
            'aiRequest' => $aiRequest,
            'doctorName' => $doctorName,
            'patientName' => $patientName,
        ]);

        return $pdf->download('ordonnance-ia-'.$aiRequest->id.'.pdf');
    }

    /**
     * Convert structured values from model output into safe printable text.
     *
     * @return array<string, mixed>
     */
    private function normalizeResult(?string $content): array
    {
        $result = $content ? json_decode($content, true) : [];
        if (! is_array($result)) {
            return [];
        }

        foreach (['diagnostic', 'traitement', 'suivi', 'precautions'] as $field) {
            if (isset($result[$field]) && ! is_string($result[$field]) && ! is_numeric($result[$field])) {
                $result[$field] = $this->formatAiValue($result[$field]);
            }
        }

        foreach (['medicaments', 'medicaments_suivi'] as $field) {
            if (isset($result[$field]) && is_array($result[$field])) {
                $result[$field] = array_map(
                    fn (mixed $item): string => $this->formatAiValue($item),
                    array_values($result[$field]),
                );
            }
        }

        return $result;
    }

    private function formatAiValue(mixed $value): string
    {
        if (is_scalar($value)) {
            return (string) $value;
        }

        if (! is_array($value)) {
            return '';
        }

        $parts = array_map(fn (mixed $part): string => $this->formatAiValue($part), $value);

        return implode(' — ', array_filter($parts, fn (string $part): bool => $part !== ''));
    }
}
