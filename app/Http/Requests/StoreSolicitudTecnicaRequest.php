<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudTecnicaRequest extends FormRequest
{
    /** Endpoint público: no requiere usuario autenticado. */
    public function authorize(): bool
    {
        return true;
    }

    /** Saneamiento previo: elimina HTML y espacios antes de validar. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cliente_nombre'     => strip_tags(trim((string) $this->input('cliente_nombre', ''))),
            'cliente_telefono'   => preg_replace('/[\s\-\.]/', '', (string) $this->input('cliente_telefono', '')),
            'descripcion_averia' => strip_tags(trim((string) $this->input('descripcion_averia', ''))),
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_nombre'     => ['required', 'string', 'min:3', 'max:100'],
            // Teléfono español: móvil o fijo, con prefijo +34/0034 opcional.
            'cliente_telefono'   => ['required', 'string', 'regex:/^(\+34|0034)?[6789]\d{8}$/'],
            'tipo_aparato'       => ['required', 'string', 'in:aire_acondicionado,lavadora,caldera,frigorifico,otro'],
            'descripcion_averia' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_telefono.regex' => 'El teléfono debe ser un número español válido (9 dígitos, prefijo +34 opcional).',
            'tipo_aparato.in'        => 'El tipo de aparato seleccionado no es válido.',
            'descripcion_averia.min' => 'Describe la avería con al menos 10 caracteres.',
        ];
    }
}
