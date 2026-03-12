@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Roles y Permisos" />

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    {{-- Header con título y botones --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Gestión de Roles y Permisos
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                Administra los roles del sistema y sus permisos asociados
            </p>
        </div>

        <div class="flex gap-3">
            <x-form.button href="{{ route('roles.create') }}" variant="primary" size="md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Rol
            </x-form.button>
        </div>
    </div>

    {{-- Tabla de Roles --}}
    <x-tables.table
        title="Roles del Sistema"
        :headers="['Rol', 'Guard', 'Usuarios', 'Permisos', 'Fecha Creación']"
        :paginator="$roles"
        :searchable="true"
        emptyMessage="No hay roles registrados"
    >
        @foreach($roles as $role)
        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
            <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 font-semibold text-sm">
                        {{ strtoupper(substr($role->name, 0, 1)) }}
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $role->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            ID: #{{ $role->id }}
                        </div>
                    </div>
                </div>
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded-full">
                    {{ $role->guard_name }}
                </span>
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <span class="text-sm text-gray-900 dark:text-white font-medium">
                        {{ $role->users_count ?? 0 }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                        usuarios
                    </span>
                </div>
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-900 dark:text-white font-medium">
                        {{ $role->permissions_count ?? 0 }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        permisos
                    </span>
                    @if(($role->permissions_count ?? 0) > 0)
                    <span class="inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                    @endif
                </div>
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $role->created_at->format('d/m/Y') }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $role->created_at->format('H:i') }}
                </div>
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">
                    <a href="{{ route('roles.show', $role) }}"
                       class="hover:text-blue-500 transition-colors"
                       title="Ver detalles">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>

                    <a href="{{ route('roles.edit', $role) }}"
                       class="hover:text-blue-500 transition-colors"
                       title="Editar rol">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>

                    @if($role->name !== 'Administrador')
                    <form action="{{ route('roles.destroy', $role) }}"
                          method="POST"
                          onsubmit="return confirm('¿Estás seguro de eliminar este rol? Los usuarios perderán estos permisos.')"
                          class="inline">
                        @csrf @method('DELETE')
                        <button class="hover:text-red-500 transition-colors" title="Eliminar rol">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
    </x-tables.table>
</div>
@endsection
