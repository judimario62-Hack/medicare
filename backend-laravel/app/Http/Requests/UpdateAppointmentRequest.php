<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', Rule::exists('doctors', 'id')->where('is_active', true)],
            'room_id' => ['nullable', 'integer', Rule::exists('rooms', 'id')->where('is_active', true)],
            'consultation_type' => ['nullable', Rule::in(['clinic', 'home'])],
            'home_address' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', Rule::in(['scheduled', 'pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show'])],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
