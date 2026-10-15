<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolicitudTecnicaRequest;
use App\Http\Resources\SolicitudTecnicaResource;
use App\Mail\NuevaSolicitudTecnica;
use App\Models\SolicitudTecnica;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactoController extends Controller
{
    /** POST /api/contacto — registra una solicitud de servicio técnico. */
    public function store(StoreSolicitudTecnicaRequest $request): JsonResponse
    {
        $solicitud = SolicitudTecnica::create($request->validated());

        // Aviso por email al dueño. Es SECUNDARIO respecto a guardar la
        // solicitud: si el servidor de correo falla, el cliente no debe
        // recibir un error (su solicitud YA está registrada), así que se
        // captura la excepción y solo se deja constancia en el log.
        try {
            Mail::to(config('mrclima.email_avisos'))
                ->send(new NuevaSolicitudTecnica($solicitud));
        } catch (Throwable $e) {
            Log::error('No se pudo enviar el aviso de nueva solicitud técnica', [
                'solicitud_id' => $solicitud->id,
                'error'        => $e->getMessage(),
            ]);
        }

        return (new SolicitudTecnicaResource($solicitud))
            ->additional(['message' => 'Solicitud recibida. Te contactaremos en breve.'])
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
