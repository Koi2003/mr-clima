<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación del alta de una lavadora desde el panel de administración.
 * La ruta ya está protegida por auth:sanctum, por eso authorize() es true.
 */
class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Saneamiento previo: sin HTML en los campos de texto libre. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'marca'       => strip_tags(trim((string) $this->input('marca', ''))),
            'modelo'      => strip_tags(trim((string) $this->input('modelo', ''))),
            'descripcion' => $this->filled('descripcion')
                ? strip_tags(trim((string) $this->input('descripcion')))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'marca'                 => ['required', 'string', 'min:2', 'max:60'],
            'modelo'                => ['required', 'string', 'min:2', 'max:100'],
            'precio'                => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'meses_garantia'        => ['required', 'integer', 'min:1', 'max:36'],
            'capacidad_kg'          => ['nullable', 'numeric', 'min:1', 'max:25'],
            'rpm'                   => ['nullable', 'integer', 'min:400', 'max:2000'],
            'eficiencia_energetica' => ['nullable', 'string', 'in:A,B,C,D,E,F,G'],
            'notas_tecnicas'        => ['nullable', 'string', 'max:2000'],
            'descripcion'           => ['nullable', 'string', 'max:2000'],
            'condicion'             => ['nullable', 'string', 'in:reacondicionado,nuevo'],
            'garantia_tipo'         => ['nullable', 'string', 'max:100'],
            'imagen_url'            => ['nullable', 'string', 'max:2048'],
            'imagenes'              => ['nullable', 'array', 'max:5'],
            /*
             * 'nullable' a secas aceptaba cualquier archivo: un .php, un .exe
             * o un PDF de 200 MB. El límite de 5 MB es el que anuncia la
             * propia caja de subida del panel.
             */
            'imagenes.*'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'precio.max'         => 'El precio supera el máximo permitido (999999.99).',
            'meses_garantia.max' => 'La garantía no puede superar los 36 meses.',
            'imagen_url.url'     => 'La imagen debe ser una URL http(s) válida.',
        ];
    }
}
