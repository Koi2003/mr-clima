<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\PedidoAdminController;
use App\Http\Controllers\Api\Admin\ProductoAdminController;
use App\Http\Controllers\Api\Admin\SolicitudAdminController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ContactoController;
use App\Http\Controllers\Api\ReservaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/
Route::get('/lavadoras', [CatalogoController::class, 'index']);
Route::get('/lavadoras/{producto}', [CatalogoController::class, 'show'])
    ->whereNumber('producto');

// Rate limiting: los formularios públicos son objetivo habitual de spam/bots.
Route::middleware('throttle:10,1')->group(function (): void {
    Route::post('/contacto', [ContactoController::class, 'store']);
    Route::post('/lavadoras/{id}/reservar', ReservaController::class)
        ->whereNumber('id');
    Route::post('/checkout/comprar', [CheckoutController::class, 'store']);
});

Route::post('/admin/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // Freno a fuerza bruta.

/*
|--------------------------------------------------------------------------
| Panel de administración (protegido con Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('admin')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gestión de solicitudes de servicio técnico.
    Route::get('/solicitudes', [SolicitudAdminController::class, 'index']);
    Route::patch('/solicitudes/{solicitud}', [SolicitudAdminController::class, 'update']);

    // Gestión de pedidos cobrados/enviados.
    Route::get('/pedidos', [PedidoAdminController::class, 'index']);
    Route::patch('/pedidos/{pedido}', [PedidoAdminController::class, 'update']);

    // CRUD completo del inventario de lavadoras (index/show/store/update/destroy).
    Route::apiResource('productos', ProductoAdminController::class);
});
