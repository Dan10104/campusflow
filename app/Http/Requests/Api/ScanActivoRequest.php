<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ScanActivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_code_id' => ['required', 'string'],
            'accion'     => ['required', 'string', 'in:prestamo,devolucion'],
        ];
    }
}
