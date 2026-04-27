@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Categorías" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Gestión de Categorías
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                    Administra las clasificaciones de tus productos
                </p>
            </div>

            {{-- Botón estandarizado con nuestro componente --}}
            <x-form.button href="{{ route('categories.create') }}" variant="primary" size="md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Categoría
            </x-form.button>
        </div>

        {{-- Tabla de Categorías --}}
        <x-tables.table 
            title="Listado de categorías" 
            :headers="['Categoría', 'Descripción']" 
            :paginator="$categories" 
            :searchable="true"
            emptyMessage="No hay categorías registradas"
        >
            @foreach ($categories as $category)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    
                    {{-- Nombre --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $category->name }}
                        </span>
                    </td>

                    {{-- Descripción (Truncada para evitar scroll horizontal) --}}
                    <td class="px-4 py-4 max-w-[200px] sm:max-w-xs md:max-w-sm lg:max-w-md">
                        <span class="block text-sm text-gray-600 dark:text-gray-400 truncate" title="{{ $category->description }}">
                            {{ $category->description ?? 'Sin descripción' }}
                        </span>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">
                            
                            {{-- Botón Ver (Opcional: puedes dejarlo como modal o crear la vista show) --}}
                            {{-- Por ahora, si no tienes vista show, podemos dejarlo como modal de solo lectura --}}
                            <x-modal.details title="Detalles de la Categoría" size="md">
                                <x-slot name="trigger">
                                    <button class="hover:text-blue-500 transition-colors p-1" title="Ver detalles">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <div class="space-y-4">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <p class="text-xs text-gray-500 uppercase mb-1">Nombre</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <p class="text-xs text-gray-500 uppercase mb-1">Descripción</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $category->description ?? 'Sin descripción' }}</p>
                                    </div>
                                </div>
                            </x-modal.details>

                            {{-- Botón Editar (Página dedicada) --}}
                            <a href="{{ route('categories.edit', $category->id) }}"
                                class="hover:text-yellow-600 transition-colors p-1" 
                                title="Editar categoría">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>

                            {{-- Modal de Confirmación para Eliminar (Único modal que se mantiene por seguridad) --}}
                            <x-modal.confirmation 
                                title="Eliminar categoría" 
                                :message="'¿Estás seguro que deseas eliminar la categoría'" 
                                :itemName="$category->name" {{-- Corregido el typo 'namee' --}}
                                warning="Esta acción es irreversible y fallará si hay productos vinculados." 
                                confirmText="Sí, eliminar"
                                confirmVariant="danger" 
                                :action="route('categories.destroy', $category->id)" 
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
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection