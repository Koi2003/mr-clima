<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Webhook firmado de Stripe.
 *
 * Es la ÚNICA garantía fiable de que un pago se confirma, porque no depende
 * de que el comprador vuelva a la web. Sin él, si el cliente cierra la
 * pestaña al pagar, el dinero entra en Stripe pero el pedido queda pendiente
 * y la lavadora acaba liberándose como si no se hubiera vendido.
 *
 * Eventos que se atienden:
 *  - checkout.session.completed -> cierra la venta (pagado + vendido)
 *  - checkout.session.expired   -> la sesión caducó sin pagar: libera el stock
 */
class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $firma   = (string) $request->header('Stripe-Signature', '');
        $secreto = (string) config('services.stripe.webhook_secret');

        if (!$this->firmaValida($payload, $firma, $secreto)) {
            Log::warning('Webhook de Stripe con firma inválida o ausente');

            return response()->json(['error' => 'Firma inválida'], 400);
        }

        $evento = json_decode($payload, true);
        $evento = is_array($evento) ? $evento : [];

        $tipo   = (string) ($evento['type'] ?? '');
        $objeto = $evento['data']['object'] ?? [];
        $objeto = is_array($objeto) ? $objeto : [];

        match ($tipo) {
            'checkout.session.completed' => $this->cerrarVenta($objeto),
            'checkout.session.expired'   => $this->liberarSesionCaducada($objeto),
            default                      => null,
        };

        // Stripe reintenta la entrega si no recibe un 2xx.
        return response()->json(['status' => 'ok']);
    }

    /** Pago completado: pedido pagado y lavadora marcada como vendida. */
    private function cerrarVenta(array $sesion): void
    {
        if (($sesion['payment_status'] ?? null) !== 'paid') {
            return;
        }

        DB::transaction(function () use ($sesion): void {
            $pedido = Pedido::with('producto')
                ->where('transaction_id', $sesion['id'] ?? '')
                ->lockForUpdate()
                ->first();

            if ($pedido && $pedido->estado_pago !== 'pagado') {
                $pedido->update(['estado_pago' => 'pagado']);
                $pedido->producto?->marcarComoVendido();
            }
        });
    }

    /**
     * La sesión caducó sin pagarse: la lavadora vuelve al catálogo ya.
     *
     * Se marca como 'fallido' y no como 'cancelado' porque la columna
     * estado_pago es un ENUM('pendiente','pagado','fallido'): un valor fuera
     * del ENUM hace que MySQL rechace el UPDATE, revierte la transacción y
     * deja el stock bloqueado para siempre (mientras Stripe reintenta).
     *
     * Cualquier fallo aquí se registra y se traga: si devolviéramos un 5xx,
     * Stripe reintentaría el evento en bucle sin arreglar nada.
     */
    private function liberarSesionCaducada(array $sesion): void
    {
        try {
            DB::transaction(function () use ($sesion): void {
                $pedido = Pedido::with('producto')
                    ->where('transaction_id', $sesion['id'] ?? '')
                    ->lockForUpdate()
                    ->first();

                if ($pedido && $pedido->estado_pago === 'pendiente') {
                    $pedido->update(['estado_pago' => 'fallido']);
                    $pedido->producto?->liberar();
                }
            });
        } catch (\Throwable $e) {
            Log::error('No se pudo liberar el stock de una sesión de Stripe caducada', [
                'session_id' => $sesion['id'] ?? null,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Comprueba la firma del webhook: HMAC-SHA256 sobre "timestamp.payload"
     * con el secreto del endpoint. Se rechazan los eventos de más de 5 minutos
     * para evitar ataques de repetición.
     */
    private function firmaValida(string $payload, string $cabecera, string $secreto): bool
    {
        if ($cabecera === '' || $secreto === '') {
            return false;
        }

        $marca = null;
        $firma = null;

        foreach (explode(',', $cabecera) as $parte) {
            [$clave, $valor] = array_pad(explode('=', trim($parte), 2), 2, null);

            if ($clave === 't') {
                $marca = (int) $valor;
            }

            if ($clave === 'v1') {
                $firma = (string) $valor;
            }
        }

        if ($marca === null || $firma === null || abs(time() - $marca) > 300) {
            return false;
        }

        $esperada = hash_hmac('sha256', $marca . '.' . $payload, $secreto);

        return hash_equals($esperada, $firma);
    }
}
