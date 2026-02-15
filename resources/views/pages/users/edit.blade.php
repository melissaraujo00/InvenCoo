@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Editar Usuario" />

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    {{-- Header con título y botones --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Editar Usuario: {{ $user->name }} {{ $user->last_name }}
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                Modifique los campos que desea actualizar
            </p>
        </div>

        <div class="flex gap-3">
            {{-- Badge de estado --}}
            <div class="flex items-center px-4 py-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">Estado actual:</span>
                <x-tables.status-badge :status="$user->status" />
            </div>

            <x-form.button href="{{ route('users.index') }}" variant="secondary" size="md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado
            </x-form.button>
        </div>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Tarjeta: Información Personal --}}
        <x-common.component-card title="Información Personal" class="mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Columna izquierda --}}
                    <div class="space-y-6">
                        {{-- Nombre --}}
                        <x-form.group name="name" label="Nombre" :required="true">
                            <x-form.input
                                name="name"
                                placeholder="Ej. Juan Carlos"
                                :required="true"
                                :value="$user->name"
                            />
                        </x-form.group>

                        {{-- Email --}}
                        <x-form.group name="email" label="Correo Electrónico" :required="true">
                            <x-form.input
                                name="email"
                                type="email"
                                placeholder="ejemplo@correo.com"
                                :required="true"
                                :value="$user->email"
                            />
                            @if($user->email_verified_at)
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verificado
                                </p>
                            @endif
                        </x-form.group>

                        {{-- Número de teléfono/ID --}}
                        <x-form.group name="number" label="Número de Teléfono / ID" :required="true">
                            <x-form.input
                                name="number"
                                placeholder="0412-1234567"
                                :required="true"
                                :value="$user->number"
                            />
                        </x-form.group>
                    </div>

                    {{-- Columna derecha --}}
                    <div class="space-y-6">
                        {{-- Apellido --}}
                        <x-form.group name="last_name" label="Apellido" :required="true">
                            <x-form.input
                                name="last_name"
                                placeholder="Ej. Pérez Martínez"
                                :required="true"
                                :value="$user->last_name"
                            />
                        </x-form.group>

                        {{-- Oficina --}}
                        <x-form.group name="office_id" label="Oficina Asignada" :required="true">
                            <x-form.select
                                name="office_id"
                                :options="$offices->pluck('name', 'id')"
                                :value="$user->office_id"
                                placeholder="Seleccionar oficina"
                            />
                        </x-form.group>

                        {{-- Estado --}}
                        <x-form.group name="status" label="Estado del Usuario">
                            <x-form.select
                                name="status"
                                :options="['1' => 'Activo', '0' => 'Inactivo']"
                                :value="$user->status"
                                placeholder="Seleccionar estado"
                            />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Los usuarios inactivos no pueden acceder al sistema
                            </p>
                        </x-form.group>
                    </div>
                </div>

                {{-- Separador --}}
                <hr class="border-gray-200 dark:border-gray-700 my-6">

                {{-- Sección de Roles --}}
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">
                        Roles y Permisos
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                       @foreach($roles as $role)
                            <div class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <input type="checkbox"
                                    name="roles[]"
                                    value="{{ $role->id }}"
                                    id="role_{{ $role->id }}"
                                    class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700"
                                    {{ in_array($role->id, old('roles', $userRoleIds)) ? 'checked' : '' }}>
                                <label for="role_{{ $role->id }}" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="text-theme-xs text-error-500 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Separador --}}
                <hr class="border-gray-200 dark:border-gray-700 my-6">

                {{-- Sección de Cambio de Contraseña --}}
                <div>
                    {{-- Alerta informativa --}}
                    <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <span class="font-semibold">Importante:</span> Complete los siguientes campos solo si desea cambiar la contraseña actual. Si la contraseña no necesita cambios, déjelos en blanco.
                                </p>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">
                        Cambiar Contraseña
                    </h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Nueva Contraseña --}}
                        <x-form.password
                            name="password"
                            label="Nueva Contraseña"
                            placeholder="Mínimo 8 caracteres"
                        />

                        {{-- Confirmar Nueva Contraseña --}}
                        <x-form.group name="password_confirmation" label="Confirmar Nueva Contraseña">
                            <x-form.input
                                type="password"
                                name="password_confirmation"
                                placeholder="Repite la nueva contraseña"
                            />
                        </x-form.group>
                    </div>
                </div>
            </div>
        </x-common.component-card>

        {{-- Tarjeta: Auditoría --}}
        <x-common.component-card title="Información de Auditoría" class="mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha de creación</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">
                            {{ $user->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Última actualización</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">
                            {{ $user->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    @if($user->email_verified_at)
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email verificado</p>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400 mt-1">
                            {{ $user->email_verified_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @else
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email verificado</p>
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400 mt-1">
                            Pendiente de verificación
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </x-common.component-card>

        {{-- Botones de acción --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
            <x-form.button type="button" href="{{ route('users.index') }}" variant="secondary" size="lg">
                Cancelar
            </x-form.button>

            <x-form.button type="submit" variant="primary" size="lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar Usuario
            </x-form.button>
        </div>
    </form>
</div>
@endsection
