@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Compras" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Listado de Compras
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                    Historial de adquisiciones y entradas de almacén
                </p>
            </div>
            <x-form.button href="{{ route('buys.create') }}" variant="primary" size="md">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Compra
            </x-form.button>
        </div>

        {{-- Tabla de Compras (Sin 'Acciones' en el header para evitar duplicidad) --}}
        <x-tables.table
            title="Compras registradas"
            :headers="['ID', 'Fecha', 'Proveedor', 'Total', 'Responsable', 'Oficina', 'Estado']"
            :paginator="$buys"
            :searchable="true"
            emptyMessage="No hay compras registradas"
        >
            @foreach($buys as $buy)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">
                        #{{ $buy->id }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($buy->date)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $buy->supplier->company_name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                        ${{ number_format($buy->total, 2) }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $buy->user->name }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $buy->office->name }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($buy->is_cancelled)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-500/10 dark:text-red-400">
                                Cancelada
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-500/10 dark:text-green-400">
                                Activa
                            </span>
                        @endif
                    </td>
                    
                    {{-- Acciones Estandarizadas --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">
                            {{-- Ver --}}
                            <a href="{{ route('buys.show', $buy->id) }}" class="hover:text-blue-500 transition-colors" title="Ver detalle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            {{-- Editar --}}
                            @role('Administrador')
                                @if(!$buy->is_cancelled)
                                    <a href="{{ route('buys.edit', $buy->id) }}" class="hover:text-yellow-600 transition-colors" title="Editar compra">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endif
                            @endrole

                            {{-- Anular/Restaurar con Modales de Confirmación --}}
                            @if(!$buy->is_cancelled)
                                <x-modal.confirmation 
                                    title="Anular Compra" 
                                    :message="'¿Estás seguro que deseas anular la compra'" 
                                    :itemName="'#' . $buy->id"
                                    warning="Se restará el stock de los productos comprados y se registrará un movimiento de salida." 
                                    confirmText="Sí, anular"
                                    confirmVariant="danger" 
                                    :action="route('buys.cancel', $buy->id)" 
                                    method="PATCH" 
                                    icon="danger"
                                >
                                    <x-slot name="trigger">
                                        <button class="hover:text-red-500 transition-colors" title="Anular compra">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </x-slot>
                                </x-modal.confirmation>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection