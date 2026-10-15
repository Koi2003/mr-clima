<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/** Se lanza dentro de la transacción de reserva; provoca rollback automático. */
class ProductoNoDisponibleException extends RuntimeException
{
    public function __construct(string $message = 'La lavadora ya no está disponible.')
    {
        parent::__construct($message);
    }
}
