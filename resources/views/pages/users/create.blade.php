@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Crear Usuario" />

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    {{-- Header con título y botones --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Crear Nuevo Usuario
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                Complete los campos para registrar un nuevo usuario en el sistema
            </p>
        </div>

        <div class="flex gap-3">
            <x-form.button href="{{ route('users.index') }}" variant="secondary" size="md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado
            </x-form.button>
        </div>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('users.store') }}" method="POST">
        @csrf

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
                                :value="old('name')"
                            />
                        </x-form.group>

                        {{-- Email --}}
                        <x-form.group name="email" label="Correo Electrónico" :required="true">
                            <x-form.input
                                name="email"
                                type="email"
                                placeholder="ejemplo@correo.com"
                                :required="true"
                                :value="old('email')"
                            />
                        </x-form.group>

                        {{-- Número de teléfono/ID --}}
                        <x-form.group name="number" label="Número de Teléfono / ID" :required="true">
                            <x-form.input
                                name="number"
                                placeholder="0412-1234567"
                                :required="true"
                                :value="old('number')"
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
                                :value="old('last_name')"
                            />
                        </x-form.group>

                        {{-- Oficina --}}
                        <x-form.group name="office_id" label="Oficina Asignada" :required="true">
                            <x-form.select
                                name="office_id"
                                :options="$offices->pluck('name', 'id')"
                                :value="old('office_id')"
                                placeholder="Seleccionar oficina"
                            />
                        </x-form.group>

                        {{-- Estado (toggle) --}}
                        <x-form.group name="status" label="Estado inicial">
                            <x-form.toggle
                                name="status"
                                label="Activar usuario inmediatamente"
                                :checked="old('status', true)"
                            />
                        </x-form.group>
                    </div>
                </div>

                {{-- Separador para sección de contraseña --}}
                <hr class="border-gray-200 dark:border-gray-700 my-6">

                {{-- Título de sección de seguridad --}}
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">
                    Seguridad
                </h3>

                {{-- Campos de contraseña en grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Contraseña (el componente password ya incluye su propio label) --}}
                    <x-form.password
                        name="password"
                        label="Contraseña"
                        placeholder="Mínimo 8 caracteres"
                        :required="true"
                    />

                    {{-- Confirmación (usamos input normal porque es solo confirmación) --}}
                    <x-form.group name="password_confirmation" label="Confirmar Contraseña" :required="true">
                        <x-form.input
                            type="password"
                            name="password_confirmation"
                            placeholder="Repite la contraseña"
                            :required="true"
                        />
                    </x-form.group>
                </div>

                {{-- Barra de fortaleza de contraseña --}}
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Requisitos de seguridad:</span> La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y caracteres especiales para máxima seguridad.
                    </p>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Crear Usuario
            </x-form.button>
        </div>
    </form>
</div>
@endsection
