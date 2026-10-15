<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
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

        return DB::transaction(function () use ($validated, $request): JsonResponse {
            $producto = Producto::where('id', $validated['producto_id'])
                ->lockForUpdate()
                ->first();

            if (!$producto || !$producto->estaDisponible()) {
                return response()->json([
                    'error'   => 'stock_no_disponible',
                    'message' => 'Este producto ya no está disponible para su compra.',
                ], 409);
            }

            // Reservamos la unidad mientras se tramita (evita doble venta).
            $producto->reservar();

            // --- Contra reembolso: pedido directo en estado pendiente ---
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
                    'transaction_id'    => 'COD-' . strtoupper(uniqid()),
                ]);

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

            // --- Tarjeta (Stripe): crear sesión de pago ---
            $amount      = (int) round($producto->precio * 100);
            $successUrl = rtrim($request->root(), '/') . route('checkout.success', [], false) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl  = rtrim($request->root(), '/') . '/';

            $payload = [
                'mode'                                                  => 'payment',
                'success_url'                                           => $successUrl,
                'cancel_url'                                            => $cancelUrl,
                'line_items[0][quantity]'                               => '1',
                'line_items[0][price_data][currency]'                   => 'eur',
                'line_items[0][price_data][unit_amount]'                => (string) $amount,
                'line_items[0][price_data][product_data][name]'         => $producto->marca . ' ' . $producto->modelo,
                'line_items[0][price_data][product_data][description]'  => $producto->descripcion ?? 'Lavadora reacondicionada con garantía',
                'metadata[producto_id]'                                 => (string) $producto->id,
                'metadata[cliente_telefono]'                            => $validated['cliente_telefono'],
            ];

            if (!empty($validated['cliente_email'])) {
                $payload['customer_email'] = $validated['cliente_email'];
            }

            $response = Http::asForm()
                ->withToken(config('services.stripe.secret'))
                ->acceptJson()
                ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

            if (!$response->successful()) {
                // Si el pago no se puede iniciar, devolvemos la lavadora al
                // catalogo: la reserva se hizo ANTES de llamar a Stripe, y el
                // retorno normal de la transaccion la confirmaria igualmente.
                $producto->liberar();

                return response()->json([
                    'message' => 'No se pudo iniciar el pago con Stripe. Inténtalo de nuevo.',
                    'details' => $response->json('error.message'),
                ], 502);
            }

            $session = $response->json();

            if (empty($session['url'])) {
                $producto->liberar();

                return response()->json([
                    'message' => 'No se pudo iniciar el pago con Stripe. Inténtalo de nuevo.',
                ], 502);
            }

            $pedido = Pedido::create([
                'producto_id'       => $producto->id,
                'cliente_nombre'    => $validated['cliente_nombre'],
                'cliente_telefono'  => $validated['cliente_telefono'],
                'cliente_email'     => $validated['cliente_email'] ?? null,
                'cliente_direccion' => $validated['cliente_direccion'],
                'cliente_localidad' => $validated['cliente_localidad'] ?? 'Alicante / Vega Baja',
                'total'             => $producto->precio,
                'metodo_pago'       => 'stripe',
                'estado_pago'       => 'pendiente',
                'estado_envio'      => 'pendiente',
                'transaction_id'    => $session['id'],
            ]);

            return response()->json([
                'message' => 'Redirigiendo al pago seguro...',
                'data'    => [
                    'pedido_id'    => $pedido->id,
                    'checkout_url' => $session['url'],
                ],
            ], 201);
        });
    }
}
