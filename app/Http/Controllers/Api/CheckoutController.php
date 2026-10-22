<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AvisoTaller;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class CheckoutController extends Controller
{
    /*
    | Una sesión de pago con tarjeta no dura 48 horas. Si el cliente abandona
    | la pasarela, la lavadora debe volver al catálogo cuanto antes (el cron
    | `reservas:liberar` la suelta al caducar).
    |
    | La reserva es algo mayor que la vida de la sesión de Stripe, para que la
    | sesión muera siempre antes de que la unidad se libere.
    */
    private const MINUTOS_SESION_STRIPE = 31;

    private const MINUTOS_RESERVA_TARJETA = 40;

    /**
     * POST /api/checkout/comprar
     * Procesa la compra directa de un producto (Nuevo o Reacondicionado).
     * - 'efectivo': crea el pedido en estado pendiente (pago contra reembolso).
     * - 'stripe': crea una sesión de pago de Stripe y devuelve la URL de pago.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'producto_id'       => ['required', 'integer', 'exists:productos,id'],
            'cliente_nombre'    => ['required', 'string', 'max:150'],
            'cliente_telefono'  => ['required', 'string', 'max:30'],
            'cliente_email'     => ['nullable', 'email', 'max:150'],
            'cliente_direccion' => ['required', 'string', 'max:255'],
            'cliente_localidad' => ['nullable', 'string', 'max:100'],
            'metodo_pago'       => ['required', 'in:stripe,efectivo'],
        ]);

        /*
        | 1) Bloqueo y reserva en una transacción CORTA.
        |
        | Aquí no se llama a ningún servicio externo a propósito: mantener una
        | fila y una conexión MySQL bloqueadas mientras se espera a Stripe
        | agota el pool de conexiones (letal en hosting compartido).
        */
        $producto = DB::transaction(function () use ($validated): ?Producto {
            $producto = Producto::where('id', $validated['producto_id'])
                ->lockForUpdate()
                ->first();

            if (!$producto || !$producto->estaDisponible()) {
                return null;
            }

            $producto->reservar(
                $validated['metodo_pago'] === 'stripe' ? self::MINUTOS_RESERVA_TARJETA : null
            );

            return $producto;
        });

        if ($producto === null) {
            return response()->json([
                'error'   => 'stock_no_disponible',
                'message' => 'Este producto ya no está disponible para su compra.',
            ], 409);
        }

        // 2) Contra reembolso: pedido directo en estado pendiente.
        if ($validated['metodo_pago'] === 'efectivo') {
            $pedido = Pedido::create([
                'producto_id'       => $producto->id,
                'cliente_nombre'    => $validated['cliente_nombre'],
                'cliente_telefono'  => $validated['cliente_telefono'],
                'cliente_email'     => $validated['cliente_email'] ?? null,
                'cliente_direccion' => $validated['cliente_direccion'],
                'cliente_localidad' => $validated['cliente_localidad'] ?? 'Alicante / Vega Baja',
                'total'             => $producto->precio,
                'metodo_pago'       => 'efectivo',
                'estado_pago'       => 'pendiente',
                'estado_envio'      => 'pendiente',
                // Referencia legible; no es un identificador criptográfico,
                // pero tampoco lo es un pago en mano.
                'transaction_id'    => 'COD-' . Str::upper(Str::random(12)),
            ]);

            /*
            | El pago en mano NO es una reserva: el pedido está cerrado y la
            | lavadora tiene dueño. Si se dejara en 'reservado' con el plazo
            | de 48 h del config, el cron `reservas:liberar` la devolvería al
            | catálogo el domingo y podría venderse dos veces.
            */
            $producto->marcarComoVendido();

            $this->avisarPedido($pedido, $producto);

            return response()->json([
                'message' => '¡Pedido realizado! Pagarás al recibir tu lavadora.',
                'data'    => [
                    'pedido_id'      => $pedido->id,
                    'transaction_id' => $pedido->transaction_id,
                    'total'          => (float) $pedido->total,
                    'metodo_pago'    => $pedido->metodo_pago,
                    'estado_pago'    => $pedido->estado_pago,
                ],
            ], 201);
        }

        // 3) Tarjeta: la sesión de pago se crea FUERA de la transacción.
        return $this->iniciarPagoConTarjeta($producto, $validated);
    }

    /**
     * Aviso al taller de que ha entrado un pedido.
     *
     * Es secundario respecto al pedido: si el correo falla, el pedido ya está
     * guardado y el cliente no debe ver un error (perdería la venta por un
     * problema del servidor de correo).
     */
    private function avisarPedido(Pedido $pedido, Producto $producto): void
    {
        try {
            $esTarjeta = $pedido->metodo_pago === 'stripe';

            Mail::to(config('mrclima.email_avisos'))->send(new AvisoTaller(
                asunto: "Nuevo pedido #{$pedido->id} — {$producto->marca} {$producto->modelo}",
                titulo: "Mr. Clima — Nuevo pedido #{$pedido->id}",
                filas: [
                    'Lavadora'   => "{$producto->marca} {$producto->modelo}",
                    'Total'      => number_format((float) $pedido->total, 2, ',', '.') . ' €',
                    'Pago'       => $esTarjeta
                        ? 'Tarjeta — pendiente de confirmación de Stripe'
                        : 'Contra reembolso, al entregar',
                    'Cliente'    => $pedido->cliente_nombre,
                    'Teléfono'   => $pedido->cliente_telefono,
                    'Email'      => $pedido->cliente_email ?? '—',
                    'Dirección'  => trim($pedido->cliente_direccion . ' (' . $pedido->cliente_localidad . ')'),
                    'Referencia' => $pedido->transaction_id,
                ],
                nota: $esTarjeta
                    ? 'El pedido se cerrará solo cuando Stripe confirme el cobro (webhook).'
                    : 'Pedido contra reembolso: llámalo para acordar la entrega.',
            ));
        } catch (Throwable $e) {
            Log::error('No se pudo enviar el aviso de nuevo pedido', [
                'pedido_id' => $pedido->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Crea la sesión de pago en Stripe.
     *
     * Si algo falla (respuesta de error, red caída o timeout) se libera la
     * lavadora: la reserva se hizo antes, así que hay que deshacerla.
     */
    private function iniciarPagoConTarjeta(Producto $producto, array $datos): JsonResponse
    {
        try {
            /*
            | URLs absolutas construidas desde APP_URL (route()/config), nunca
            | desde $request->root(): una cabecera Host manipulada redirigiría
            | al comprador —y con él su session_id— a un dominio ajeno.
            */
            $successUrl = route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl  = (string) config('app.url');

            $payload = [
                'mode'                                                 => 'payment',
                'success_url'                                          => $successUrl,
                'cancel_url'                                           => $cancelUrl,
                'expires_at'                                           => now()->addMinutes(self::MINUTOS_SESION_STRIPE)->timestamp,
                'line_items[0][quantity]'                              => '1',
                'line_items[0][price_data][currency]'                  => 'eur',
                'line_items[0][price_data][unit_amount]'               => (string) (int) round((float) $producto->precio * 100),
                'line_items[0][price_data][product_data][name]'        => $producto->marca . ' ' . $producto->modelo,
                'line_items[0][price_data][product_data][description]' => $producto->descripcion ?? 'Lavadora reacondicionada con garantía',
                'metadata[producto_id]'                                => (string) $producto->id,
                'metadata[cliente_telefono]'                           => $datos['cliente_telefono'],
            ];

            if (!empty($datos['cliente_email'])) {
                $payload['customer_email'] = $datos['cliente_email'];
            }

            $response = Http::asForm()
                ->withToken(config('services.stripe.secret'))
                // Sin timeout, una lentitud de Stripe bloquea un proceso PHP
                // (y su worker) de forma indefinida.
                ->timeout(10)
                ->acceptJson()
                ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

            $session = $response->json();

            if (!$response->successful() || empty($session['url']) || empty($session['id'])) {
                $producto->liberar();

                Log::error('No se pudo crear la sesión de pago de Stripe', [
                    'producto_id' => $producto->id,
                    'stripe'      => $session['error']['message'] ?? 'respuesta sin URL',
                ]);

                return response()->json([
                    'message' => 'No se pudo iniciar el pago. Inténtalo de nuevo.',
                ], 502);
            }

            $pedido = Pedido::create([
                'producto_id'       => $producto->id,
                'cliente_nombre'    => $datos['cliente_nombre'],
                'cliente_telefono'  => $datos['cliente_telefono'],
                'cliente_email'     => $datos['cliente_email'] ?? null,
                'cliente_direccion' => $datos['cliente_direccion'],
                'cliente_localidad' => $datos['cliente_localidad'] ?? 'Alicante / Vega Baja',
                'total'             => $producto->precio,
                'metodo_pago'       => 'stripe',
                'estado_pago'       => 'pendiente',
                'estado_envio'      => 'pendiente',
                'transaction_id'    => $session['id'],
            ]);

            $this->avisarPedido($pedido, $producto);

            return response()->json([
                'message' => 'Redirigiendo al pago seguro...',
                'data'    => [
                    'pedido_id'    => $pedido->id,
                    'checkout_url' => $session['url'],
                ],
            ], 201);
        } catch (Throwable $e) {
            // Acción compensatoria: sin esto la lavadora quedaría bloqueada
            // aunque el pago no haya llegado a iniciarse.
            $producto->liberar();

            Log::critical('Excepción al contactar con Stripe', [
                'producto_id' => $producto->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'No pudimos conectar con la pasarela de pago. Inténtalo de nuevo.',
            ], 502);
        }
    }
}
