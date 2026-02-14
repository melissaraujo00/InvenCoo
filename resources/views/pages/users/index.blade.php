@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Usuarios" />

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Gestión de Usuarios
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Administra los accesos de tu plataforma
            </p>
        </div>

        <a href="{{ route('users.create') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-colors">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                <path d="M10.8333 5V9.16667H15V10.8333H10.8333V15H9.16667V10.8333H5V9.16667H9.16667V5H10.8333Z" />
            </svg>
            Agregar Usuario
        </a>
    </div>

    {{-- Usando el componente genérico de tabla --}}
    <x-tables.table
        title="Lista de Usuarios"
        :headers="['Usuario', 'Correo Electrónico', 'Estado', 'Rol', 'Sucursal']"
        :paginator="$users"
        :searchable="true"
        emptyMessage="No hay usuarios registrados"
    >
        @foreach($users as $user)
        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
            <td class="px-4 py-4 whitespace-nowrap">
                <x-tables.initials-avatar
                    :name="$user->name"
                    :lastName="$user->last_name"
                    :id="$user->id"
                />
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-white">{{ $user->email }}</div>
                @if($user->email_verified_at)
                <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                    ✓ Verificado
                </div>
                @endif
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <x-tables.status-badge :status="$user->status" />
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $user->role->name ?? 'Sin rol' }}
                </span>
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $user->office->name ?? 'Sin sucursal' }}
                </span>
            </td>

            <td class="px-4 py-4 whitespace-nowrap">
                <x-tables.actions
                    :id="$user->id"
                    editRoute="users.edit"
                    deleteRoute="users.destroy"
                />
            </td>
        </tr>
        @endforeach
    </x-tables.table>
</div>
@endsection
