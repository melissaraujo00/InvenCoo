<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está logueado pero su cuenta fue desactivada
        if (Auth::check() && !Auth::user()->status) { // <--- Cambia 'is_active' por el nombre real de tu columna

            Auth::logout(); // Lo cerramos la sesión forzosamente
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Lo mandamos a la pantalla de login con un mensaje de error
            return redirect()->route('login')->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
        }

        return $next($request);
    }
}
