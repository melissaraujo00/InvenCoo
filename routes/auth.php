<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Rutas para invitados (no autenticados)
Route::middleware('guest')->group(function () {
    // Mostrar formulario de inicio de sesión
    Route::get('/signin', function () {
        return view('pages.auth.signin', ['title' => 'Sign In']);
    })->name('login');

    // Procesar inicio de sesión (sin nombre para no duplicar 'login')
    Route::post('/signin', [AuthController::class, 'login']);
});

// Cerrar sesión (requiere autenticación)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
