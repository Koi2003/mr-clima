<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EstadoStock;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CheckoutSuccessController extends Controller
{
    /**
     * GET /checkout/exito — confirmación del pago tras volver de Stripe.
     */
    public function show(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return view('checkout.success', [
                'confirmed' => false,
                'error'     => 'Falta el identificador de la sesión de pago.',
            ]);
        }

        $session = Http::asForm()
            ->withToken(config('services.stripe.secret'))
            ->acceptJson()
            ->get('https://api.stripe.com/v1/checkout/sessions/' . $sessionId)
            ->json();

        if (!isset($session['id']) || ($session['payment_status'] ?? null) !== 'paid') {
            return view('checkout.success', [
                'confirmed' => false,
                'error'     => 'El pago no se ha completado correctamente.',
            ]);
        }

        $pedido = null;

        DB::transaction(function () use ($session, &$pedido): void {
            $pedido = Pedido::where('transaction_id', $session['id'])->first();

            if ($pedido && $pedido->estado_pago !== 'pagado') {
                $pedido->update(['estado_pago' => 'pagado']);
                $pedido->producto?->update([
                    'estado_stock'    => EstadoStock::Vendido,
                    'reservado_hasta' => null,
                ]);
            }
        });

        return view('checkout.success', [
            'confirmed' => true,
            'pedido'    => $pedido,
        ]);
    }
}
