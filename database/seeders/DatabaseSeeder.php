<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        | Administrador del panel.
        | La contraseña sale de config('mrclima.admin_password') —que a su vez
        | lee ADMIN_PASSWORD del .env—. Se usa config() y NO env() porque con
        | la caché de configuración activa (config:cache, obligatoria en
        | producción) env() devuelve siempre null.
        | Si no está definida, se genera una aleatoria y se muestra UNA vez.
        | Para cambiarla después: php artisan admin:password
        */
        $passwordConfigurada = config('mrclima.admin_password');
        $password = (string) ($passwordConfigurada ?: Str::password(16));

        $admin = User::firstOrCreate(
            ['email' => 'admin@misterclima.es'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make($password),
            ]
        );

        if ($admin->wasRecentlyCreated && ! $passwordConfigurada) {
            $this->command?->warn("Contraseña del administrador generada: {$password}");
            $this->command?->warn('Apúntala ahora: no se volverá a mostrar.');
        }

        // Lavadoras de ejemplo (unidades únicas).
        $lavadoras = [
            ['marca' => 'Bosch',   'modelo' => 'Serie 4 WAN28281ES', 'precio' => 249.99, 'meses_garantia' => 12, 'descripcion' => 'Lavadora 8kg, 1400 rpm, clase C. Reacondicionada y revisada.'],
            ['marca' => 'Balay',   'modelo' => '3TS883BE',           'precio' => 199.00, 'meses_garantia' => 6,  'descripcion' => 'Lavadora 8kg, 1000 rpm. Reacondicionada, con pequeña marca estética lateral.'],
            ['marca' => 'LG',      'modelo' => 'F4WV3008S6W',        'precio' => 279.50, 'meses_garantia' => 12, 'descripcion' => 'Lavadora 8kg AI DD, motor Direct Drive. Reacondicionada.'],
            ['marca' => 'Samsung', 'modelo' => 'WW90TA046AE',        'precio' => 259.00, 'meses_garantia' => 6,  'descripcion' => 'Lavadora 9kg EcoBubble. Reacondicionada y revisada.'],
        ];

        foreach ($lavadoras as $lavadora) {
            Producto::firstOrCreate(
                ['marca' => $lavadora['marca'], 'modelo' => $lavadora['modelo']],
                $lavadora
            );
        }
    }
}
