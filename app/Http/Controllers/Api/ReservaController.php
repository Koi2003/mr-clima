<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\ProductoNoDisponibleException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Mail\AvisoTaller;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ReservaController extends Controller
{
    /**
     * POST /api/lavadoras/{id}/reservar
     *
     * Reserva atómica de una unidad única. El SELECT ... FOR UPDATE bloquea
     * la fila hasta el COMMIT: si dos peticiones llegan a la vez, la segunda
     * espera, relee el estado ya actualizado y recibe un 409.
     */
    public function __invoke(int $id): JsonResponse
    {
        try {
            /** @var Producto $producto */
            $producto = DB::transaction(function () use ($id): Producto {
                $producto = Producto::query()
                    ->whereKey($id)
                    ->lockForUpdate()   // Bloqueo pesimista a nivel de fila (InnoDB).
                    ->firstOrFail();

                if (! $producto->estaDisponible()) {
                    // Lanzar dentro de la transacción fuerza el rollback.
                    throw new ProductoNoDisponibleException();
                }

                // Marca 'reservado' y fija la caducidad (config: mrclima.reserva_horas).
                // Si nadie confirma la compra antes de ese plazo, el comando
                // programado reservas:liberar la devolverá al catálogo.
                $producto->reservar();

                return $producto;
            });
        } catch (ProductoNoDisponibleException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error'   => 'stock_no_disponible',
            ], JsonResponse::HTTP_CONFLICT); // 409
        }

        // Aviso al taller. Secundario: si el correo falla, la reserva ya está
        // hecha y el cliente no debe ver un error.
        try {
            Mail::to(config('mrclima.email_avisos'))->send(new AvisoTaller(
                asunto: "Nueva reserva: {$producto->marca} {$producto->modelo}",
                titulo: 'Mr. Clima — Nueva reserva de lavadora',
                filas: [
                    'Lavadora'  => "{$producto->marca} {$producto->modelo}",
                    'Precio'    => number_format((float) $producto->precio, 2, ',', '.') . ' €',
                    'Reservada hasta' => optional($producto->reservado_hasta)->format('d/m/Y H:i') ?? '—',
                    'Unidad'    => '#' . $producto->id,
                ],
                nota: 'La reserva se hizo desde el catálogo público, sin datos de contacto: '
                    . 'el cliente tiene 48 h para confirmar la compra.',
            ));
        } catch (Throwable $e) {
            Log::error('No se pudo enviar el aviso de nueva reserva', [
                'producto_id' => $producto->id,
                'error'       => $e->getMessage(),
            ]);
        }

        return (new ProductoResource($producto))
            ->additional(['message' => 'Lavadora reservada correctamente.'])
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
