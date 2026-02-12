<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/signup', function () {
        return view('pages.auth.signup', ['title' => 'Sign Up']);
    })->name('signup');

   // routes/auth.php
Route::post('/signin', [AuthController::class, 'login'])->name('login');

    Route::get('/signin', function () {
        return view('pages.auth.signin', ['title' => 'Sign In']);
    })->name('login');

    Route::post('/signup', [AuthController::class, 'register'])->name('signup');


});

// routes/auth.php
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
