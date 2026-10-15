<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoStock: string
{
    case Disponible = 'disponible';
    case Reservado  = 'reservado';
    case Vendido    = 'vendido';
}
