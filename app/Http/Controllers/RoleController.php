<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Can;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount(['users', 'permissions']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('guard_name', 'like', "%{$search}%");
        }

        $roles = $query->paginate(10)->withQueryString(); // conserva el search en paginación

        return view('pages.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('pages.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    public function show(Role $role)
    {
        // Cargamos permisos
        $role->load('permissions');

        // Cargamos los usuarios que tienen este rol específico (con paginación)
        $users = \App\Models\User::role($role->name)->with('office')->paginate(10);

        return view('pages.roles.show', compact('role', 'users'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('pages.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array|nullable' // Asegura que pueda ser nulo
        ]);

        $role->update(['name' => $request->name]);

        // Si hay permisos, los sincroniza. Si el arreglo está vacío o no viene, limpia los permisos.
        $permisos = $request->input('permissions', []);
        $role->syncPermissions($permisos);

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
        return redirect()->route('roles.index')
            ->with('error', 'No se puede eliminar un rol base del sistema.');
        }

        // 2. Prevenir la eliminación de roles en uso
        if ($role->users()->exists()) { // exists() es más rápido en SQL que count() > 0
            return redirect()->route('roles.index')
                ->with('error', 'Acción denegada: Hay usuarios que actualmente tienen asignado este rol.');
        }

        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }
}
