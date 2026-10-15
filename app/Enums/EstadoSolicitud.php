<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoSolicitud: string
{
    case Pendiente  = 'pendiente';
    case EnProceso  = 'en_proceso';
    case Finalizada = 'finalizada';
}
