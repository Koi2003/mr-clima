<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Configuración de negocio de Mr. Clima
|--------------------------------------------------------------------------
| Valores parametrizables del dominio. Se pueden sobreescribir por entorno
| mediante variables en el .env sin tocar el código.
*/

return [

    /*
    | Horas que dura una reserva antes de caducar automáticamente.
    | Pasado este plazo, el comando programado `reservas:liberar`
    | devuelve la lavadora al estado 'disponible'.
    */
    'reserva_horas' => (int) env('MRCLIMA_RESERVA_HORAS', 48),

    /*
    | Contraseña inicial del administrador (opcional).
    | Se lee aquí, y no con env() dentro del seeder, porque env() devuelve
    | null en cuanto se activa la caché de configuración (config:cache),
    | que es justo lo que se hace al desplegar en producción.
    */
    'admin_password' => env('ADMIN_PASSWORD'),

    /*
    | Dirección que recibe el aviso por email cada vez que entra una
    | solicitud de servicio técnico. En producción debe ser el correo
    | que el dueño mira a diario.
    */
    'email_avisos' => env('MRCLIMA_EMAIL_AVISOS', 'info@misterclima.es'),

];
