<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\EstadoStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación de la edición parcial (PATCH) de una lavadora.
 * Todas las reglas usan 'sometimes': solo se valida lo que llega,
 * permitiendo actualizar un único campo sin reenviar el resto.
 */
class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Saneamiento previo solo de los campos presentes en la petición. */
    protected function prepareForValidation(): void
    {
        $limpios = [];

        foreach (['marca', 'modelo', 'descripcion'] as $campo) {
            // Solo se sanea si llegó un string real: un null explícito debe
            // seguir siendo null ((string) null lo convertiría en "" y se
            // guardaría una cadena vacía en lugar de vaciar el campo).
            if ($this->has($campo) && is_string($this->input($campo))) {
                $limpios[$campo] = strip_tags(trim($this->input($campo)));
            }
        }

        // Un texto que queda vacío tras sanear equivale a "sin descripción".
        if (($limpios['descripcion'] ?? null) === '') {
            $limpios['descripcion'] = null;
        }

        $this->merge($limpios);
    }

    public function rules(): array
    {
        return [
            'marca'                 => ['sometimes', 'required', 'string', 'min:2', 'max:60'],
            'modelo'                => ['sometimes', 'required', 'string', 'min:2', 'max:100'],
            'precio'                => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999.99'],
            'meses_garantia'        => ['sometimes', 'required', 'integer', 'min:1', 'max:36'],
            'capacidad_kg'          => ['sometimes', 'nullable', 'numeric', 'min:1', 'max:25'],
            'rpm'                   => ['sometimes', 'nullable', 'integer', 'min:400', 'max:2000'],
            'eficiencia_energetica' => ['sometimes', 'nullable', 'string', 'in:A,B,C,D,E,F,G'],
            'notas_tecnicas'        => ['sometimes', 'nullable', 'string', 'max:2000'],
            'descripcion'           => ['sometimes', 'nullable', 'string', 'max:2000'],
            'condicion'             => ['sometimes', 'nullable', 'string', 'in:reacondicionado,nuevo'],
            'garantia_tipo'         => ['sometimes', 'nullable', 'string', 'max:100'],
            'imagen_url'            => ['sometimes', 'nullable', 'string', 'max:2048'],
            'imagenes'              => ['sometimes', 'nullable', 'array', 'max:5'],
            'imagenes.*'            => ['nullable'],
            'estado_stock'          => ['sometimes', 'required', Rule::enum(EstadoStock::class)],
        ];
    }
}
