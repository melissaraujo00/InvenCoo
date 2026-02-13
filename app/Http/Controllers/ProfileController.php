<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        // 1. Obtenemos el usuario actual
        $user = auth()->user();

        // 2. Validamos (Ignoramos el email del propio usuario para que no de error)
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'number'    => ['required', 'string', 'max:20'],
        ]);

        // 3. Actualizamos
        $user->update($validated);

        // 4. Regresamos a la página anterior con mensaje
        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
