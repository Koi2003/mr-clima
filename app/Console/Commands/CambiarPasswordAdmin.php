<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Cambia la contraseña del administrador del panel.
 *
 * Uso:
 *   php artisan admin:password
 *   php artisan admin:password otro@correo.es
 *   php artisan admin:password --password="MiClaveSegura123!"
 */
class CambiarPasswordAdmin extends Command
{
    protected $signature = 'admin:password
                            {email=admin@misterclima.es : Email del administrador}
                            {--password= : Nueva contraseña (si se omite, se pide por consola)}';

    protected $description = 'Cambia la contraseña del administrador del panel';

    public function handle(): int
    {
        $email = (string) $this->argument('email');

        $password = (string) ($this->option('password') ?: $this->secret('Escribe la nueva contraseña'));

        if (strlen($password) < 12) {
            $this->error('La contraseña debe tener al menos 12 caracteres.');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No existe ningún administrador con el email: {$email}");

            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("✅ Contraseña actualizada correctamente para {$email}");

        return self::SUCCESS;
    }
}
