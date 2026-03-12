{{-- resources/views/pages/roles/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Editar Rol" />

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Editar Rol: {{ $role->name }}
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Modifica los permisos y detalles del rol
            </p>
        </div>
    </div>

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            {{-- Nombre del Rol --}}
            <div class="mb-6">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nombre del Rol <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $role->name) }}"
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

                {{-- Agrupación de permisos por módulo --}}
                @php
                    $groupedPermissions = $permissions->groupBy(function($permission) {
                        $parts = explode(' ', $permission->name);
                        return end($parts); // Agrupa por la última palabra (usuarios, roles, etc)
                    });
                @endphp

                <div class="space-y-4">
                    @foreach($groupedPermissions as $group => $permisos)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <h4 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">
                                {{ ucfirst($group) }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($permisos as $permission)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ $permission->name }}
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
                    Actualizar Rol
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
