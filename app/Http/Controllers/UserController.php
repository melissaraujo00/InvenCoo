<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['office', 'roles'])
            ->when($request->filled('search'), fn($query) =>
                $query->whereAny(['name', 'last_name', 'email', 'number'], 'LIKE', "%{$request->search}%")
            )
            ->paginate(10);

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $offices = Office::all();
        $roles = Role::all(); // <- ESTABA FALTANDO ESTA LÍNEA

        return view('pages.users.create', compact('offices', 'roles')); // <- AGREGAR 'roles' AL COMPACT
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Hash de la contraseña
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
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

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }
}
