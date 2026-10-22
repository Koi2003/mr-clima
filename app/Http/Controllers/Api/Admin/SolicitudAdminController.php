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

        /*
         * El KPI se cuenta en la base de datos, no filtrando json.data: antes
         * el panel contaba solo los elementos de la página visible (20) y el
         * número cambiaba al aplicar el filtro de la tabla, que no es lo que
         * mide un indicador global.
         */
        return SolicitudTecnicaResource::collection($solicitudes)
            ->additional([
                'kpis' => [
                    'pendientes' => SolicitudTecnica::query()
                        ->where('estado', EstadoSolicitud::Pendiente->value)
                        ->count(),
                ],
            ]);
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
