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

        // 1. Sanitización estricta del número de teléfono
        if (!empty($data['number'])) {
            // Quitamos cualquier espacio, guion o carácter raro
            $cleanNumber = preg_replace('/[^0-9]/', '', $data['number']);
            // Si el número no empieza con 503, se lo agregamos
            if (!str_starts_with($cleanNumber, '503')) {
                $cleanNumber = '503' . $cleanNumber;
            }
            $data['number'] = $cleanNumber;
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

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

        // 1. Sanitización estricta del número de teléfono
        if (!empty($data['number'])) {
            $cleanNumber = preg_replace('/[^0-9]/', '', $data['number']);
            if (!str_starts_with($cleanNumber, '503')) {
                $cleanNumber = '503' . $cleanNumber;
            }
            $data['number'] = $cleanNumber;
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

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
