@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detalle de Movimiento" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Movimiento #{{ $movement->id }}
            </h2>
            <x-form.button href="{{ route('movements.index') }}" variant="secondary" size="md">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </x-form.button>
        </div>

        {{-- Tarjeta de información --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Transacción</span>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $movement->transaction_id }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha</span>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $movement->date_movement }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Oficina</span>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $movement->office->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo</span>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $movement->type->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Entrada / Salida</span>
                    <p class="text-base font-semibold">
                        @if($movement->input_type == 'E')
                            <span class="text-green-600 dark:text-green-400">Entrada</span>
                        @else
                            <span class="text-red-600 dark:text-red-400">Salida</span>
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuario</span>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $movement->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</span>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90">
                        {{ number_format($movement->details->sum('subtotal'), 2) }}
                    </p>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</span>
                    <p class="text-base text-gray-700 dark:text-gray-300">{{ $movement->description ?: 'Sin descripción' }}</p>
                </div>
                @if($movement->origin_office_id)
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Oficina Origen</span>
                        <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $movement->originatingBranch->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Oficina Destino</span>
                        <p class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $movement->destinationBranch->name ?? 'N/A' }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabla de productos (sin columna Subtotal) --}}
        <x-tables.table
            title="Productos del Movimiento"
            :headers="['Producto', 'Cantidad', 'Precio Unitario', 'Stock después']"
            emptyMessage="No hay productos en este movimiento"
        >
            @foreach($movement->details as $detail)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $detail->product->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $detail->quantity }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ number_format($detail->unit_price, 2) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $detail->stock_after }}
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection
