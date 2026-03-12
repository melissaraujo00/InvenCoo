@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Detalle del Rol" />

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    {{-- Header con título y botones --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Rol: {{ $role->name }}
                </h2>
                <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400 rounded-full">
                    ID: #{{ $role->id }}
                </span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                Gestiona los permisos asignados a este rol
            </p>
        </div>

        <div class="flex gap-3">
            <x-form.button href="{{ route('roles.index') }}" variant="secondary" size="md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </x-form.button>

            <x-form.button href="{{ route('roles.edit', $role) }}" variant="primary" size="md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar Rol
            </x-form.button>
        </div>
    </div>

    {{-- Tarjeta de Información del Rol --}}
    <x-common.component-card title="Información del Rol" class="mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre del Rol</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $role->name }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Guard</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $role->guard_name }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuarios Asignados</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $users->total() ?? 0 }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha Creación</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $role->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Última Actualización</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $role->updated_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Permisos</p>
                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">{{ $role->permissions->count() }}</p>
                </div>
            </div>
        </div>
    </x-common.component-card>

    {{-- Tarjeta de Permisos del Rol --}}
    <x-common.component-card title="Permisos Asignados al Rol" class="mb-6">
        <div class="p-6">
            {{-- Agrupación de permisos por módulo --}}
            @php
                $permissionsByModule = $role->permissions->groupBy(function($permission) {
                    $parts = explode('.', $permission->name);
                    return $parts[0] ?? 'general';
                });
            @endphp

            @forelse($permissionsByModule as $module => $permissions)
                <div class="mb-6 last:mb-0">
                    <div class="flex items-center mb-3">
                        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            {{ ucfirst($module) }}
                        </h4>
                        <span class="ml-3 px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full">
                            {{ $permissions->count() }} permisos
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($permissions as $permission)
                            <div class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-green-50 dark:bg-green-500/10 border-green-200 dark:border-green-800">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $permission->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $permission->id }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Este rol no tiene permisos asignados</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Asigna permisos desde la edición del rol</p>
                </div>
            @endforelse
        </div>
    </x-common.component-card>

    {{-- Tarjeta de Usuarios con este Rol --}}
    @if($users->count() > 0)
    <x-common.component-card title="Usuarios con este Rol" class="mb-6">
        <div class="p-6">
            <x-tables.table
                :headers="['Usuario', 'Correo', 'Oficina', 'Estado']"
                :paginator="$users"
                :searchable="false"
            >
                @foreach($users as $user)
                <tr>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <x-tables.initials-avatar
                            :name="$user->name"
                            :lastName="$user->last_name"
                            :id="$user->id"
                        />
                    </td>

                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $user->email }}</div>
                    </td>

                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $user->office->name ?? 'Sin oficina' }}
                        </span>
                    </td>

                    <td class="px-4 py-4 whitespace-nowrap">
                        <x-tables.status-badge :status="$user->status" />
                    </td>

                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex justify-end">
                            <a href="{{ route('users.edit', $user) }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                Ver Usuario
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-tables.table>
        </div>
    </x-common.component-card>
    @endif
</div>
@endsection
