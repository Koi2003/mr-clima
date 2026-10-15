<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Enums\EstadoSolicitud;
use App\Http\Controllers\Controller;
use App\Http\Resources\SolicitudTecnicaResource;
use App\Models\SolicitudTecnica;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class SolicitudAdminController extends Controller
{
    /** GET /api/admin/solicitudes?estado=pendiente */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Validar el filtro: un estado inexistente debe dar 422, no una
        // lista vacía que haga creer al panel que "no hay solicitudes".
        $validated = $request->validate([
            'estado' => ['sometimes', Rule::enum(EstadoSolicitud::class)],
        ]);

        $solicitudes = SolicitudTecnica::query()
            ->when(
                $validated['estado'] ?? null,
                fn ($q, string $estado) => $q->where('estado', $estado)
            )
            ->latest()
            ->paginate(perPage: 20)
            ->withQueryString(); // Mantiene el filtro al paginar.

        return SolicitudTecnicaResource::collection($solicitudes);
    }

    /** PATCH /api/admin/solicitudes/{solicitud} — cambio de estado explícito. */
    public function update(Request $request, SolicitudTecnica $solicitud): SolicitudTecnicaResource
    {
        $validated = $request->validate([
            'estado' => ['required', Rule::enum(EstadoSolicitud::class)],
        ]);

        $solicitud->estado = EstadoSolicitud::from($validated['estado']);
        $solicitud->save();

        return new SolicitudTecnicaResource($solicitud);
    }
}
