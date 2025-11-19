<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow any authenticated user to submit an incident. The specific
        // access control for who can create incidents is handled by route
        // middleware using role restrictions. Returning true here
        // prevents the form request from rejecting all requests by default.
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
            // Optional additional metadata about the incident. These fields
            // correspond to columns added by the migration
            'severity' => 'nullable|in:baja,media,alta,critica',
            'location' => 'nullable|string|max:255',
            'resolution_status' => 'nullable|in:pendiente,en_proceso,resuelto',
            'actions_taken' => 'nullable|string',
            // Keep resolved optional; it may be set via the UI when toggling resolution
            'resolved' => 'nullable|boolean',
        ];
    }
}
