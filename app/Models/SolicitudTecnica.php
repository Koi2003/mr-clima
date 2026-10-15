<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoSolicitud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudTecnica extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_tecnicas';

    protected $fillable = [
        'cliente_nombre',
        'cliente_telefono',
        'tipo_aparato',
        'descripcion_averia',
    ];
    // 'estado' queda fuera de fillable a propósito: solo lo cambia el admin
    // mediante asignación explícita, nunca por mass-assignment del formulario público.

    // Default también en memoria: el modelo recién creado ya tiene estado
    // sin necesidad de refresh() tras el INSERT (el default de MySQL solo
    // aplica en la BD, no en la instancia de Eloquent).
    protected $attributes = [
        'estado' => 'pendiente',
    ];

    protected $hidden = [
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoSolicitud::class,
        ];
    }
}
