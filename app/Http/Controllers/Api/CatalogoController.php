<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogoController extends Controller
{
    /**
     * GET /api/lavadoras
     * Catálogo público con filtros opcionales: ?precio_max=300&marca=Bosch
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'precio_max' => ['sometimes', 'numeric', 'min:0'],
            'marca'      => ['sometimes', 'string', 'max:60'],
        ]);

        $productos = Producto::query()
            ->disponibles()
            ->marca($validated['marca'] ?? null)
            ->precioMax(isset($validated['precio_max']) ? (float) $validated['precio_max'] : null)
            ->orderBy('precio')
            ->paginate(perPage: 12)
            // Propaga los filtros activos a los enlaces de paginación:
            // sin esto, la página 2 perdería precio_max y marca.
            ->withQueryString();

        return ProductoResource::collection($productos);
    }

    /** GET /api/lavadoras/{producto} — ficha individual. */
    public function show(Producto $producto): ProductoResource
    {
        return new ProductoResource($producto);
    }
}
