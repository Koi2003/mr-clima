<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoStock;
use App\Models\Producto;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_compra_directa_marca_producto_como_vendido_y_crea_pedido(): void
    {
        $producto = Producto::factory()->create([
            'estado_stock' => EstadoStock::Disponible->value,
            'precio'       => 249.99,
        ]);

        $response = $this->postJson('/api/checkout/comprar', [
            'producto_id'       => $producto->id,
            'cliente_nombre'    => 'María García',
            'cliente_telefono'  => '600123456',
            'cliente_direccion' => 'Calle Mayor 12, Almoradí',
            'metodo_pago'       => 'efectivo',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.total', 249.99)
            ->assertJsonPath('data.metodo_pago', 'efectivo');

        $this->assertDatabaseHas('productos', [
            'id'           => $producto->id,
            'estado_stock' => EstadoStock::Vendido->value,
        ]);

        $this->assertDatabaseHas('pedidos', [
            'producto_id'       => $producto->id,
            'cliente_nombre'    => 'María García',
            'cliente_telefono'  => '600123456',
            'cliente_direccion' => 'Calle Mayor 12, Almoradí',
            'metodo_pago'       => 'efectivo',
        ]);
    }

    public function test_no_se_puede_comprar_producto_vendido_o_reservado(): void
    {
        $producto = Producto::factory()->create([
            'estado_stock' => EstadoStock::Vendido->value,
        ]);

        $response = $this->postJson('/api/checkout/comprar', [
            'producto_id'       => $producto->id,
            'cliente_nombre'    => 'María García',
            'cliente_telefono'  => '600123456',
            'cliente_direccion' => 'Calle Mayor 12, Almoradí',
            'metodo_pago'       => 'efectivo',
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('error', 'stock_no_disponible');
    }
}
