<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Enums\EstadoStock;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * CRUD completo del inventario para el panel de administración.
 * Todas las rutas cuelgan de /api/admin y están protegidas por auth:sanctum.
 */
class ProductoAdminController extends Controller
{
    /**
     * GET /api/admin/productos?estado=reservado
     * A diferencia del catálogo público, lista TODAS las unidades
     * (disponibles, reservadas y vendidas), con filtro opcional por estado.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'estado' => ['sometimes', Rule::enum(EstadoStock::class)],
        ]);

        $productos = Producto::query()
            ->when(
                $validated['estado'] ?? null,
                fn ($q, string $estado) => $q->where('estado_stock', $estado)
            )
            ->latest()          // Las últimas altas primero.
            ->paginate(perPage: 20)
            ->withQueryString(); // Mantiene el filtro al paginar.

        return ProductoResource::collection($productos);
    }

    /** GET /api/admin/productos/{producto} — detalle de una unidad. */
    public function show(Producto $producto): ProductoResource
    {
        return new ProductoResource($producto);
    }

    /** POST /api/admin/productos — alta de una lavadora en el inventario. */
    public function store(StoreProductoRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $storedImages = [];

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('productos', 'public');
                    $storedImages[] = $path;
                }
            }
        }

        if (count($storedImages) > 0) {
            $validated['imagenes'] = $storedImages;
            $validated['imagen_url'] = $storedImages[0];
        }

        $producto = Producto::create($validated);

        return (new ProductoResource($producto))
            ->additional(['message' => 'Lavadora añadida al inventario.'])
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * PATCH /api/admin/productos/{producto} — edición parcial.
     * El cambio de estado se hace bajo lock para no chocar con una
     * reserva de cliente que esté ocurriendo en ese mismo instante.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): ProductoResource
    {
        $validated = $request->validated();

        if ($request->hasFile('imagenes')) {
            $storedImages = [];
            foreach ($request->file('imagenes') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('productos', 'public');
                    $storedImages[] = $path;
                }
            }
            if (count($storedImages) > 0) {
                $validated['imagenes'] = $storedImages;
                $validated['imagen_url'] = $storedImages[0];
            }
        }

        $producto = DB::transaction(function () use ($producto, $validated): Producto {
            // Se relee la fila con lock: entre el route-model-binding y este
            // punto, un cliente pudo haberla reservado.
            $producto = Producto::query()
                ->whereKey($producto->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $producto->fill($validated);

            // Coherencia estado/caducidad al cambiar el estado a mano:
            if (array_key_exists('estado_stock', $validated)) {
                if ($producto->estado_stock === EstadoStock::Reservado) {
                    $producto->reservado_hasta ??= now()->addHours(config('mrclima.reserva_horas'));
                } else {
                    $producto->reservado_hasta = null;
                }
            }

            $producto->save();

            return $producto;
        });

        return (new ProductoResource($producto))
            ->additional(['message' => 'Lavadora actualizada.']);
    }

    /**
     * DELETE /api/admin/productos/{producto} — baja del inventario.
     * Solo se pueden borrar unidades 'disponible':
     *  - vendidas: son historial de ventas.
     *  - reservadas: un cliente la está esperando; hay que cancelar la
     *    reserva primero (PATCH estado_stock=disponible) de forma consciente.
     */
    public function destroy(Producto $producto): JsonResponse
    {
        if ($producto->estado_stock === EstadoStock::Vendido) {
            return response()->json([
                'message' => 'No se puede eliminar una lavadora vendida: forma parte del historial.',
                'error'   => 'producto_vendido',
            ], JsonResponse::HTTP_CONFLICT); // 409
        }

        if ($producto->estado_stock === EstadoStock::Reservado) {
            return response()->json([
                'message' => 'La lavadora tiene una reserva activa: cancela la reserva antes de eliminarla.',
                'error'   => 'producto_reservado',
            ], JsonResponse::HTTP_CONFLICT); // 409
        }

        $producto->delete();

        return response()->json(['message' => 'Lavadora eliminada del inventario.']);
    }
}
