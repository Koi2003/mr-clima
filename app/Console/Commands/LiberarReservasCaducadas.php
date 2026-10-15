<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Devuelve al catálogo las lavadoras cuya reserva ha caducado.
 *
 * Se ejecuta automáticamente vía scheduler (ver routes/console.php),
 * pero también puede lanzarse a mano: `php artisan reservas:liberar`.
 */
class LiberarReservasCaducadas extends Command
{
    /** Firma del comando en la CLI de artisan. */
    protected $signature = 'reservas:liberar';

    protected $description = 'Libera las lavadoras cuya reserva ha superado el plazo configurado';

    public function handle(): int
    {
        // Transacción + lockForUpdate: si un admin está marcando como vendida
        // una de estas unidades justo ahora, no pisamos su cambio a mitad.
        $liberadas = DB::transaction(function (): int {
            $productos = Producto::query()
                ->reservasCaducadas()   // reservado + reservado_hasta < now()
                ->lockForUpdate()
                ->get();

            // Se libera una a una con el método de dominio (no con un UPDATE
            // masivo) para mantener una única fuente de verdad de la lógica.
            $productos->each(fn (Producto $producto) => $producto->liberar());

            return $productos->count();
        });

        $this->info("Reservas caducadas liberadas: {$liberadas}");

        return self::SUCCESS;
    }
}
