<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow an authenticated user to update an incident. Specific
        // authorization based on role is enforced via middleware on the route.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'description' => 'required|string|max:255',
            'type' => 'required|in:accidente,retraso,mecanico,otro',
            'reported_at' => 'required|date_format:Y-m-d\TH:i',
            'severity' => 'nullable|in:baja,media,alta,critica',
            'location' => 'nullable|string|max:255',
            'resolution_status' => 'nullable|in:pendiente,en_proceso,resuelto',
            'actions_taken' => 'nullable|string',
            'resolved' => 'nullable|boolean',
        ];
    }
}
