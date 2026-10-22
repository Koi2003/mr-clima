<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoAdminController extends Controller
{
    /** GET /api/admin/pedidos */
    public function index(Request $request): JsonResponse
    {
        $query = Pedido::with('producto')->latest();

        if ($request->has('estado_pago') && !empty($request->estado_pago)) {
            $query->where('estado_pago', $request->estado_pago);
        }

        if ($request->has('estado_envio') && !empty($request->estado_envio)) {
            $query->where('estado_envio', $request->estado_envio);
        }

        $pedidos = $query->paginate(15);

        return response()->json($pedidos);
    }

    /**
     * PATCH /api/admin/pedidos/{pedido}
     *
     * Cambiar el estado del pago a mano (por ejemplo al cobrar en mano una
     * entrega) también mueve la lavadora: si no, el pedido se marcaba como
     * pagado y la unidad seguía apareciendo como reservada o disponible en el
     * catálogo, con el riesgo de venderla dos veces.
     */
    public function update(Request $request, Pedido $pedido): JsonResponse
    {
        $validated = $request->validate([
            'estado_pago'  => ['sometimes', 'in:pendiente,pagado,fallido'],
            'estado_envio' => ['sometimes', 'in:pendiente,en_reparto,entregado'],
        ]);

        DB::transaction(function () use ($pedido, $validated): void {
            $pedido->update($validated);

            $producto = $pedido->producto;

            if ($producto === null) {
                return;
            }

            match ($validated['estado_pago'] ?? null) {
                'pagado'  => $producto->marcarComoVendido(),
                'fallido' => $producto->liberar(),
                default   => null,
            };
        });

        return response()->json([
            'message' => 'Pedido actualizado correctamente',
            'data'    => $pedido->fresh('producto'),
        ]);
    }
}
