<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoStock;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_solo_productos_disponibles_ordenados_por_precio(): void
    {
        Producto::factory()->create([
            'marca' => 'Balay',
            'precio' => 199.00,
            'estado_stock' => EstadoStock::Disponible,
        ]);
        Producto::factory()->create([
            'marca' => 'Bosch',
            'precio' => 249.99,
            'estado_stock' => EstadoStock::Disponible,
        ]);
        Producto::factory()->create([
            'marca' => 'LG',
            'precio' => 279.50,
            'estado_stock' => EstadoStock::Reservado,
        ]);

        $response = $this->getJson('/api/lavadoras');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.marca', 'Balay')
            ->assertJsonPath('data.1.marca', 'Bosch');
    }

    public function test_filtra_por_precio_maximo_y_marca(): void
    {
        Producto::factory()->create([
            'marca' => 'Bosch',
            'precio' => 200.00,
            'estado_stock' => EstadoStock::Disponible,
        ]);
        Producto::factory()->create([
            'marca' => 'Bosch',
            'precio' => 300.00,
            'estado_stock' => EstadoStock::Disponible,
        ]);
        Producto::factory()->create([
            'marca' => 'Balay',
            'precio' => 150.00,
            'estado_stock' => EstadoStock::Disponible,
        ]);

        $response = $this->getJson('/api/lavadoras?precio_max=250&marca=Bosch');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.marca', 'Bosch');
    }

    public function test_precio_maximo_cero_devuelve_lista_vacia(): void
    {
        Producto::factory()->create([
            'precio' => 100.00,
            'estado_stock' => EstadoStock::Disponible,
        ]);

        $response = $this->getJson('/api/lavadoras?precio_max=0');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_precio_maximo_invalido_devuelve_422(): void
    {
        $response = $this->getJson('/api/lavadoras?precio_max=abc');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['precio_max']);
    }

    public function test_muestra_ficha_de_producto_existente(): void
    {
        $producto = Producto::factory()->create([
            'marca' => 'Samsung',
            'modelo' => 'EcoBubble',
        ]);

        $response = $this->getJson('/api/lavadoras/' . $producto->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $producto->id)
            ->assertJsonPath('data.marca', 'Samsung');
    }

    public function test_ficha_id_inexistente_devuelve_404(): void
    {
        $response = $this->getJson('/api/lavadoras/9999');

        $response->assertStatus(404);
    }
}
