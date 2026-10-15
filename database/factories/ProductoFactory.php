<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EstadoStock;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'marca' => $this->faker->company(),
            'modelo' => $this->faker->word() . ' ' . $this->faker->randomNumber(4),
            'precio' => $this->faker->randomFloat(2, 150, 400),
            'meses_garantia' => $this->faker->randomElement([6, 12, 24]),
            'descripcion' => $this->faker->sentence(),
            'estado_stock' => EstadoStock::Disponible,
            'reservado_hasta' => null,
            'imagen_url' => null,
        ];
    }
}

