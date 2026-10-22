<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Pantalla de confirmación a la que Stripe devuelve al comprador.
 *
 * Importante: es una COMODIDAD para el cliente, no la fuente de verdad. Si
 * cierra el navegador antes de llegar aquí, la venta se cierra igualmente por
 * el webhook firmado de Stripe (ver StripeWebhookController).
 */
class CheckoutSuccessController extends Controller
{
    /**
     * GET /checkout/exito — confirmación del pago.
     */
    public function show(Request $request)
    {
        $sessionId = (string) $request->query('session_id', '');

        // Formato estricto de una Checkout Session de Stripe. Evita que un
        // valor manipulado se cuele en la URL de la API (path traversal).
        if (preg_match('/^cs_(test|live)_[A-Za-z0-9]+$/', $sessionId) !== 1) {
            return view('checkout.success', [
                'confirmed' => false,
                'error'     => 'El identificador de la sesión de pago no es válido.',
            ]);
        }

        $response = Http::asForm()
            ->withToken(config('services.stripe.secret'))
            ->timeout(10)
            ->acceptJson()
            ->get('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);

        $session = $response->json();

        if (!$response->successful() || ($session['payment_status'] ?? null) !== 'paid') {
            return view('checkout.success', [
                'confirmed' => false,
                'error'     => 'El pago no se ha completado correctamente.',
            ]);
        }

        $resumen = null;

        DB::transaction(function () use ($session, &$resumen): void {
            // El lockForUpdate evita que esta pantalla y el webhook se pisen
            // si el cliente recarga justo cuando llega la notificación.
            $pedido = Pedido::with('producto')
                ->where('transaction_id', $session['id'])
                ->lockForUpdate()
                ->first();

            if (!$pedido) {
                return;
            }

            if ($pedido->estado_pago !== 'pagado') {
                $pedido->update(['estado_pago' => 'pagado']);
                // Se usa el método de dominio: un update() ignoraría
                // 'reservado_hasta' por estar fuera de $fillable.
                $pedido->producto?->marcarComoVendido();
            }

            // A la vista solo van datos NO sensibles: sin nombre, teléfono,
            // email ni dirección del comprador.
            $resumen = [
                'pedido_id' => $pedido->id,
                'total'     => (float) $pedido->total,
            ];
        });

        if ($resumen === null) {
            return view('checkout.success', [
                'confirmed' => false,
                'error'     => 'No hemos encontrado el pedido asociado al pago.',
            ]);
        }

        return view('checkout.success', [
            'confirmed' => true,
            'resumen'   => $resumen,
        ]);
    }
}
