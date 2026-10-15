<?php

use App\Http\Controllers\CheckoutSuccessController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return view('admin');
});

// Páginas legales
Route::view('/aviso-legal', 'legal.aviso-legal');
Route::view('/privacidad', 'legal.privacidad');
Route::view('/cookies', 'legal.cookies');

// Confirmación de pago (vuelta desde Stripe)
Route::get('/checkout/exito', [CheckoutSuccessController::class, 'show'])->name('checkout.success');
