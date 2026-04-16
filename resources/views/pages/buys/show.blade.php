@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detalle de Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Compra #{{ $buy->id }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Registro de compra generado el {{ $buy->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div>
                <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al listado
                </x-form.button>
            </div>
        </div>

        {{-- Tarjeta de información general --}}
        <x-common.component-card title="Información de la Compra" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-1">
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Proveedor</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $buy->supplier->company_name ?? 'No especificado' }}
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha de compra</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ \Carbon\Carbon::parse($buy->date)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Usuario responsable</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $buy->user->name ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Oficina</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $buy->office->name ?? 'N/A' }}
                    </span>
                </div>
                @if($buy->discount > 0)
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Descuento aplicado</span>
                    <span class="text-base font-medium text-green-600 dark:text-green-400">
                        ${{ number_format($buy->discount, 2) }}
                    </span>
                </div>
                @endif
                @if($buy->total_iva > 0)
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">IVA incluido</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        ${{ number_format($buy->total_iva, 2) }}
                    </span>
                </div>
                @endif
            </div>
        </x-common.component-card>

        {{-- Tabla de productos --}}
        <x-tables.table
            title="Productos Comprados"
            :headers="['Producto', 'Cantidad', 'Precio unitario', 'Subtotal']"
            emptyMessage="No hay productos registrados en esta compra">
            @foreach($buy->details as $detail)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-4 py-3">
                        <span class="font-medium text-gray-800 dark:text-white/90">{{ $detail->product->name }}</span>
                        <span class="text-xs text-gray-400 block">{{ $detail->product->code ?? '' }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $detail->quantity }}</td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">${{ number_format($detail->price, 2) }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">${{ number_format($detail->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </x-tables.table>

        {{-- Tarjeta de resumen financiero --}}
        <x-common.component-card title="Resumen Financiero" class="mt-6">
            <div class="flex flex-col items-end space-y-2 p-1">
                <div class="w-full md:w-1/2 lg:w-1/3">
                    <div class="flex justify-between py-1 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-medium text-gray-800 dark:text-white/90">${{ number_format($buy->subtotal, 2) }}</span>
                    </div>
                    @if($buy->discount > 0)
                    <div class="flex justify-between py-1 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Descuento:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">-${{ number_format($buy->discount, 2) }}</span>
                    </div>
                    @endif
                    @if($buy->total_iva > 0)
                    <div class="flex justify-between py-1 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">IVA:</span>
                        <span class="font-medium text-gray-800 dark:text-white/90">${{ number_format($buy->total_iva, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between py-2 mt-1 text-lg font-bold text-gray-900 dark:text-white">
                        <span>Total pagado:</span>
                        <span class="text-primary-600 dark:text-primary-400">${{ number_format($buy->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </x-common.component-card>
        @if($buy->is_cancelled)
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg dark:bg-red-500/20 dark:text-red-400">
        ⚠️ Esta compra ha sido cancelada.
    </div>
@endif


    </div>
@endsection
