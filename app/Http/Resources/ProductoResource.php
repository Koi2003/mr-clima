<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Producto */
class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $imagenesUrls = [];
        if (is_array($this->imagenes) && count($this->imagenes) > 0) {
            foreach ($this->imagenes as $img) {
                $imagenesUrls[] = str_starts_with($img, 'http') || str_starts_with($img, '/')
                    ? $img
                    : asset('storage/' . $img);
            }
        } elseif ($this->imagen_url) {
            $imagenesUrls[] = str_starts_with($this->imagen_url, 'http') || str_starts_with($this->imagen_url, '/')
                ? $this->imagen_url
                : asset('storage/' . $this->imagen_url);
        }

        return [
            'id'                    => $this->id,
            'marca'                 => $this->marca,
            'modelo'                => $this->modelo,
            'precio'                => (float) $this->precio,
            'meses_garantia'        => $this->meses_garantia,
            'capacidad_kg'          => $this->capacidad_kg ? (float) $this->capacidad_kg : null,
            'rpm'                   => $this->rpm,
            'eficiencia_energetica' => $this->eficiencia_energetica,
            'notas_tecnicas'        => $this->notas_tecnicas,
            'descripcion'           => $this->descripcion,
            'estado_stock'          => $this->estado_stock->value,
            'condicion'             => $this->condicion ?? 'reacondicionado',
            'garantia_tipo'         => $this->garantia_tipo ?? 'Taller Mr. Clima',
            'reservado_hasta'       => $this->reservado_hasta?->toIso8601String(),
            'imagen_url'            => count($imagenesUrls) > 0 ? $imagenesUrls[0] : $this->imagen_url,
            'imagenes'              => $imagenesUrls,
        ];
    }
}
