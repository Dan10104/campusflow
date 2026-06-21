<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:classroom,asset'],
            'id'   => ['required', 'string'],
            
            // --- Reglas exclusivas para Aulas ---
            'fecha'       => ['required_if:type,classroom', 'date_format:Y-m-d'],
            'hora_inicio' => ['required_if:type,classroom', 'date_format:H:i'],
            'hora_fin'    => ['required_if:type,classroom', 'date_format:H:i', 'after:hora_inicio'],
            
            // --- Reglas exclusivas para Activos (Préstamos) ---
            // Aquí validamos que el formato incluya fecha y hora (Y-m-d H:i)
            'fecha_inicio' => ['required_if:type,asset', 'date_format:Y-m-d H:i'],
            'fecha_fin'    => ['required_if:type,asset', 'date_format:Y-m-d H:i', 'after:fecha_inicio'],

            // Ambos comparten el propósito
            'proposito' => ['nullable', 'string', 'max:255'],
        ];
    }

}
