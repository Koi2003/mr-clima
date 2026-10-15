<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\SolicitudTecnica;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_solicitud_tecnica_con_datos_validos(): void
    {
        $payload = [
            'cliente_nombre' => '<b>Juan Pérez</b>',
            'cliente_telefono' => '612 34-56.78',
            'tipo_aparato' => 'lavadora',
            'descripcion_averia' => 'La lavadora no centrifuga y hace ruido.',
        ];

        $response = $this->postJson('/api/contacto', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.cliente_nombre', 'Juan Pérez')
            ->assertJsonPath('data.cliente_telefono', '612345678')
            ->assertJsonPath('data.estado', 'pendiente');

        $this->assertDatabaseHas('solicitudes_tecnicas', [
            'cliente_nombre' => 'Juan Pérez',
            'cliente_telefono' => '612345678',
            'tipo_aparato' => 'lavadora',
            'estado' => 'pendiente',
        ]);
    }

    public function test_rechaza_telefono_invalido(): void
    {
        $payload = [
            'cliente_nombre' => 'Juan Pérez',
            'cliente_telefono' => '12345',
            'tipo_aparato' => 'lavadora',
            'descripcion_averia' => 'La lavadora no centrifuga.',
        ];

        $response = $this->postJson('/api/contacto', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cliente_telefono']);
    }

    public function test_rechaza_tipo_aparato_invalido(): void
    {
        $payload = [
            'cliente_nombre' => 'Juan Pérez',
            'cliente_telefono' => '612345678',
            'tipo_aparato' => 'tostadora',
            'descripcion_averia' => 'La tostadora no calienta nada.',
        ];

        $response = $this->postJson('/api/contacto', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo_aparato']);
    }

    public function test_ignora_campo_estado_enviado_en_post(): void
    {
        $payload = [
            'cliente_nombre' => 'Juan Pérez',
            'cliente_telefono' => '612345678',
            'tipo_aparato' => 'lavadora',
            'descripcion_averia' => 'La lavadora pierde agua por abajo.',
            'estado' => 'finalizada',
        ];

        $response = $this->postJson('/api/contacto', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.estado', 'pendiente');
    }
}
