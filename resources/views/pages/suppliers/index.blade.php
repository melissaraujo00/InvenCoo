@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Proveedores" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Gestión de Proveedores
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                    Directorio de contactos comerciales
                </p>
            </div>
            @can('crear proveedor')
            <x-form.button href="{{ route('suppliers.create') }}" variant="primary" size="md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Proveedor
            </x-form.button>
            @endcan
        </div>

        {{-- Tabla de Proveedores (Corregidas cabeceras vs columnas) --}}
        <x-tables.table
            title="Listado de Proveedores"
            :headers="['Empresa', 'Contacto', 'Teléfono', 'Descripción']"
            :paginator="$suppliers"
            :searchable="true"
            emptyMessage="No hay proveedores registrados"
        >
            @foreach ($suppliers as $supplier)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">

                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $supplier->company_name }}
                        </span>
                    </td>

                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $supplier->contact_name }}
                        </span>
                    </td>

                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $supplier->number_phone }} {{-- Corregido, decía company_name --}}
                        </span>
                    </td>

                    {{-- Descripción Truncada --}}
                    <td class="px-4 py-4 max-w-[150px] sm:max-w-xs md:max-w-sm">
                        <span class="block text-sm text-gray-600 dark:text-gray-400 truncate" title="{{ $supplier->description }}">
                            {{ $supplier->description ?? 'Sin descripción' }}
                        </span>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">

                            {{-- Modal de Detalles --}}
                            <x-modal.details title="Ficha del Proveedor" size="lg">
                                <x-slot name="trigger">
                                    <button class="hover:text-blue-500 transition-colors p-1" title="Ver detalles">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                        <p class="text-xs text-gray-500 uppercase mb-1">Empresa</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->company_name }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                        <p class="text-xs text-gray-500 uppercase mb-1">Contacto Comercial</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->contact_name }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                        <p class="text-xs text-gray-500 uppercase mb-1">Teléfono</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->number_phone }}</p>
                                    </div>
                                    <div class="col-span-2 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                        <p class="text-xs text-gray-500 uppercase mb-1">Descripción</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $supplier->description ?? 'Sin descripción' }}</p>
                                    </div>
                                </div>
                            </x-modal.details>

                            {{-- Botón Editar --}}
                            @can('editar proveedor')
                            <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                class="hover:text-yellow-600 transition-colors p-1" title="Editar proveedor">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            @endcan

                            @can('eliminar proveedor')
                            {{-- Modal de Eliminar --}}
                            <x-modal.confirmation
                                title="Eliminar Proveedor"
                                :message="'¿Estás seguro que deseas eliminar a'"
                                :itemName="$supplier->company_name"
                                warning="Esta acción no se puede deshacer y fallará si existen productos asociados a este proveedor."
                                confirmText="Sí, eliminar"
                                confirmVariant="danger"
                                :action="route('suppliers.destroy', $supplier->id)"
                                method="DELETE"
                                icon="danger"
                            >
                                <x-slot name="trigger">
                                    <button class="hover:text-red-500 transition-colors p-1" title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </x-slot>
                            </x-modal.confirmation>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection
