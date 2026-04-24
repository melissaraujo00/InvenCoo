<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['office', 'roles'])
            ->when($request->filled('search'), fn($query) =>
                $query->whereAny(['name', 'last_name', 'email', 'number'], 'LIKE', "%{$request->search}%")
            )
            // Agregamos withQueryString() para que la paginación no pierda la búsqueda
            ->paginate(10)->withQueryString();

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $offices = Office::all();
        $roles = Role::all();

        return view('pages.users.create', compact('offices', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        
        // Optimización: syncRoles acepta IDs nativamente, no necesitamos consultar los nombres
        if ($request->has('roles') && !empty($request->roles)) {
            $user->syncRoles(array_map('intval', $request->roles));
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $offices = Office::all();
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('pages.users.edit', compact('user', 'offices', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Optimización: syncRoles directo con IDs
        if ($request->has('roles') && !empty($request->roles)) {
            $user->syncRoles(array_map('intval', $request->roles));
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        // 1. Bloqueo estricto de seguridad: Prevenir auto-eliminación
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Por razones de seguridad, no puedes eliminar tu propia cuenta.');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}