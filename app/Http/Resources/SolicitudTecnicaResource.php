<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\SolicitudTecnica */
class SolicitudTecnicaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'cliente_nombre'     => $this->cliente_nombre,
            'cliente_telefono'   => $this->cliente_telefono,
            'tipo_aparato'       => $this->tipo_aparato,
            'descripcion_averia' => $this->descripcion_averia,
            'estado'             => $this->estado->value,
            'created_at'         => $this->created_at?->toIso8601String(),
        ];
    }
}
