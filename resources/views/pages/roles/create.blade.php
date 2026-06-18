@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Crear Rol" />

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Crear Nuevo Rol
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Registra un rol y asigna sus permisos de acceso
            </p>
        </div>
    </div>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            {{-- Nombre del Rol --}}
            <div class="mb-6">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nombre del Rol <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="Ej: Administrador">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Permisos --}}
            <div class="mb-6">
                <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Permisos del Rol
                </label>

                {{-- Agrupación de permisos robusta --}}
                @php
                    $groupedPermissions = $permissions->groupBy(function($p) {
                        $name = strtolower($p->name);

                        if(str_contains($name, 'usuario')) return 'Gestión de Usuarios';
                        if(str_contains($name, 'rol') || str_contains($name, 'permiso')) return 'Roles y Permisos';
                        if(str_contains($name, 'categor')) return 'Categorías';
                        if(str_contains($name, 'marca')) return 'Marcas';
                        if(str_contains($name, 'producto') || str_contains($name, 'inventario')) return 'Productos e Inventario';
                        if(str_contains($name, 'movimiento')) return 'Movimientos';
                        if(str_contains($name, 'proveedor')) return 'Proveedores';
                        // Corrección: minúsculas y se añade 'solicit' para atrapar 'solicitar compra' y 'crear solicitud compra'
                        if(str_contains($name, 'compra') || str_contains($name, 'solicit')) return 'Compras y Solicitudes';
                        // Corrección: minúsculas
                        if(str_contains($name, 'transferencia')) return 'Transferencias';
                        if(str_contains($name, 'reporte')) return 'Reportes';

                        return 'Otros Permisos';
                    });
                @endphp

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($groupedPermissions as $group => $permisos)
                        <div class="rounded-lg border border-gray-200 p-4 bg-gray-50/50 dark:border-gray-700 dark:bg-gray-800/50">
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 border-b border-gray-200 pb-2 dark:text-white dark:border-gray-600">
                                {{ $group }}
                            </h4>
                            <div class="flex flex-col gap-3">
                                @foreach($permisos as $permission)
                                    <label class="flex items-start gap-3 cursor-pointer group">
                                        <div class="flex h-5 items-center">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->name }}"
                                                   {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}
                                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition-colors dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-500">
                                        </div>
                                        <span class="text-sm text-gray-700 group-hover:text-blue-600 transition-colors dark:text-gray-300 dark:group-hover:text-blue-400">
                                            {{ ucfirst($permission->name) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                <a href="{{ route('roles.index') }}"
                   class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Guardar Rol
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
