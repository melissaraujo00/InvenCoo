@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Usuarios" />

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Gestión de Usuarios
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Administra los usuarios y sus roles de acceso
            </p>
        </div>

        @can('crear usuarios')
        <x-form.button href="{{ route('users.create') }}" variant="primary" size="md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Usuario
        </x-form.button>
        @endcan
    </div>

    {{-- Tabla de Usuarios --}}
    <x-tables.table
        title="Lista de Usuarios"
        :headers="['Usuario', 'Correo', 'Roles', 'Oficina', 'Estado']"
        :paginator="$users"
        :searchable="true"
        emptyMessage="No hay usuarios registrados"
    >
        @foreach($users as $user)
        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
            {{-- Usuario con avatar --}}
            <td class="px-4 py-4 whitespace-nowrap">
                <x-tables.initials-avatar
                    :name="$user->name"
                    :lastName="$user->last_name"
                    :id="$user->id"
                />
            </td>

            {{-- Correo --}}
            <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-white">{{ $user->email }}</div>
                @if($user->email_verified_at)
                <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                    ✓ Verificado
                </div>
                @endif
            </td>

            {{-- Roles --}}
            <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex flex-wrap gap-1">
                    @forelse($user->roles as $role)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400">
                            {{ $role->name }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400 dark:text-gray-500">Sin rol</span>
                    @endforelse
                </div>
            </td>

            {{-- Oficina --}}
            <td class="px-4 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $user->office->name ?? 'Sin oficina' }}
                </span>
            </td>

            {{-- Estado --}}
            <td class="px-4 py-4 whitespace-nowrap">
                <x-tables.status-badge :status="$user->status" />
            </td>

            {{-- Acciones --}}
            <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">
                    {{-- Botón Editar --}}
                    @can('editar usuarios')
                    <a href="{{ route('users.edit', $user->id) }}"
                       class="hover:text-blue-500 transition-colors"
                       title="Editar usuario">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                  stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    @endcan

                    {{-- Modal de Confirmación para Eliminar --}}
                    @can('eliminar usuarios')
                        @cannot('self-delete', $user)
                        <x-modal.confirmation
                            title="Eliminar Usuario"
                            :message="'¿Estás seguro que deseas eliminar al usuario'"
                            :itemName="$user->name . ' ' . $user->last_name"
                            warning="Esta acción eliminará permanentemente el registro"
                            confirmText="Sí, eliminar"
                            confirmVariant="danger"
                            :action="route('users.destroy', $user->id)"
                            method="DELETE"
                            icon="danger"
                        >
                            <x-slot name="trigger">
                                <button class="hover:text-red-500 transition-colors" title="Eliminar">
                                    <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                              stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </x-slot>
                        </x-modal.confirmation>
                        @endcannot
                    @endcan
                </div>
            </td>
        </tr>
        @endforeach
    </x-tables.table>
</div>
@endsection
