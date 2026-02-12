<?php

namespace App\Http\Controllers;

use App\Models\User; // Importar el modelo User
use Illuminate\Http\Request;
use Inertia\Inertia; // Importar Inertia

class UserController extends Controller
{
    public function index()
    {
        // 1. Obtenemos todos los usuarios (menos la contraseña por seguridad)
        // Usamos 'paginate(10)' para que si hay muchos, no colapse la pantalla.
        $users = User::paginate(10);

        // 2. Renderizamos la vista 'Users/Index' y le pasamos los datos
        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }
}
