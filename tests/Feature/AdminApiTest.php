<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoSolicitud;
use App\Enums\EstadoStock;
use App\Models\Producto;
use App\Models\SolicitudTecnica;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'admin@misterclima.es',
            'password' => bcrypt('CambiaEsto123!'),
        ]);
    }

    public function test_login_exitoso_devuelve_token(): void
    {
        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@misterclima.es',
            'password' => 'CambiaEsto123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type']);
    }

    public function test_login_fallido_devuelve_422(): void
    {
        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@misterclima.es',
            'password' => 'Incorrecta!',
        ]);

        $response->assertStatus(422);
    }

    public function test_rutas_admin_requieren_autenticacion(): void
    {
        $response = $this->getJson('/api/admin/solicitudes');
        $response->assertStatus(401);

        $response = $this->getJson('/api/admin/productos');
        $response->assertStatus(401);
    }

    public function test_admin_puede_listar_y_filtrar_solicitudes(): void
    {
        Sanctum::actingAs($this->admin);

        SolicitudTecnica::factory()->create(['estado' => EstadoSolicitud::Pendiente]);
        SolicitudTecnica::factory()->create(['estado' => EstadoSolicitud::EnProceso]);

        $response = $this->getJson('/api/admin/solicitudes?estado=pendiente');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.estado', 'pendiente');
    }

    public function test_admin_puede_actualizar_estado_de_solicitud(): void
    {
        Sanctum::actingAs($this->admin);

        $solicitud = SolicitudTecnica::factory()->create(['estado' => EstadoSolicitud::Pendiente]);

        $response = $this->patchJson("/api/admin/solicitudes/{$solicitud->id}", [
            'estado' => 'en_proceso',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.estado', 'en_proceso');
    }

    public function test_admin_crea_producto_en_inventario(): void
    {
        Sanctum::actingAs($this->admin);

        $payload = [
            'marca' => 'Siemens',
            'modelo' => 'iQ300 Test',
            'precio' => 289.90,
            'meses_garantia' => 12,
            'descripcion' => 'Alta de prueba.',
        ];

        $response = $this->postJson('/api/admin/productos', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.marca', 'Siemens')
            ->assertJsonPath('data.estado_stock', 'disponible');
    }

    public function test_admin_edita_producto_parcialmente_y_cambia_estado(): void
    {
        Sanctum::actingAs($this->admin);

        $producto = Producto::factory()->create([
            'precio' => 200.00,
            'estado_stock' => EstadoStock::Disponible,
        ]);

        $response = $this->patchJson("/api/admin/productos/{$producto->id}", [
            'precio' => 220.00,
            'estado_stock' => 'reservado',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.estado_stock', 'reservado');

        $producto->refresh();
        $this->assertNotNull($producto->reservado_hasta);
    }

    public function test_admin_elimina_producto_disponible(): void
    {
        Sanctum::actingAs($this->admin);

        $producto = Producto::factory()->create([
            'estado_stock' => EstadoStock::Disponible,
        ]);

        $response = $this->deleteJson("/api/admin/productos/{$producto->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }

    public function test_admin_no_puede_eliminar_producto_reservado_o_vendido(): void
    {
        Sanctum::actingAs($this->admin);

        $productoReservado = Producto::factory()->create(['estado_stock' => EstadoStock::Reservado]);
        $productoVendido = Producto::factory()->create(['estado_stock' => EstadoStock::Vendido]);

        $this->deleteJson("/api/admin/productos/{$productoReservado->id}")
            ->assertStatus(409)
            ->assertJsonPath('error', 'producto_reservado');

        $this->deleteJson("/api/admin/productos/{$productoVendido->id}")
            ->assertStatus(409)
            ->assertJsonPath('error', 'producto_vendido');
    }
}
