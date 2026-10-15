<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tareas programadas
|--------------------------------------------------------------------------
| Requiere UNA sola entrada en el cron del servidor (o en el Programador
| de tareas de Windows) que ejecute cada minuto:
|
|   php artisan schedule:run
|
| En desarrollo puede simularse con: php artisan schedule:work
*/

// Libera las lavadoras cuya reserva ha caducado. Cada 15 minutos es
// suficiente: el plazo de reserva se mide en horas, no en minutos.
// withoutOverlapping evita ejecuciones solapadas si una tarda de más.
Schedule::command('reservas:liberar')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
