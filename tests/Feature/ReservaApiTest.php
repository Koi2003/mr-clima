<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoStock;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_reserva_producto_disponible_con_exito(): void
    {
        $producto = Producto::factory()->create([
            'estado_stock' => EstadoStock::Disponible,
        ]);

        $response = $this->postJson("/api/lavadoras/{$producto->id}/reservar");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Lavadora reservada correctamente.');

        $producto->refresh();
        $this->assertEquals(EstadoStock::Reservado, $producto->estado_stock);
        $this->assertNotNull($producto->reservado_hasta);
    }

    public function test_reserva_producto_ya_reservado_devuelve_409(): void
    {
        $producto = Producto::factory()->create([
            'estado_stock' => EstadoStock::Reservado,
            'reservado_hasta' => now()->addHours(48),
        ]);

        $response = $this->postJson("/api/lavadoras/{$producto->id}/reservar");

        $response->assertStatus(409)
            ->assertJsonPath('error', 'stock_no_disponible');
    }

    public function test_reserva_producto_vendido_devuelve_409(): void
    {
        $producto = Producto::factory()->create([
            'estado_stock' => EstadoStock::Vendido,
        ]);

        $response = $this->postJson("/api/lavadoras/{$producto->id}/reservar");

        $response->assertStatus(409)
            ->assertJsonPath('error', 'stock_no_disponible');
    }

    public function test_reserva_producto_inexistente_devuelve_404(): void
    {
        $response = $this->postJson("/api/lavadoras/9999/reservar");

        $response->assertStatus(404);
    }

    public function test_liberar_reservas_caducadas(): void
    {
        $productoCaducado = Producto::factory()->create([
            'estado_stock' => EstadoStock::Reservado,
            'reservado_hasta' => now()->subHour(),
        ]);

        $productoVigente = Producto::factory()->create([
            'estado_stock' => EstadoStock::Reservado,
            'reservado_hasta' => now()->addHours(24),
        ]);

        $this->artisan('reservas:liberar')
            ->expectsOutputToContain('Reservas caducadas liberadas: 1')
            ->assertExitCode(0);

        $productoCaducado->refresh();
        $productoVigente->refresh();

        $this->assertEquals(EstadoStock::Disponible, $productoCaducado->estado_stock);
        $this->assertNull($productoCaducado->reservado_hasta);

        $this->assertEquals(EstadoStock::Reservado, $productoVigente->estado_stock);
        $this->assertNotNull($productoVigente->reservado_hasta);
    }
}
