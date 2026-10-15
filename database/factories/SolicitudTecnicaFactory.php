<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EstadoSolicitud;
use App\Models\SolicitudTecnica;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudTecnicaFactory extends Factory
{
    protected $model = SolicitudTecnica::class;

    public function definition(): array
    {
        return [
            'cliente_nombre' => $this->faker->name(),
            'cliente_telefono' => '612' . $this->faker->numberBetween(100000, 999999),
            'tipo_aparato' => 'lavadora',
            'descripcion_averia' => $this->faker->sentence(10),
            'estado' => EstadoSolicitud::Pendiente,
        ];
    }
}
