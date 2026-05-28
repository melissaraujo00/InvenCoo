@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

        <div class="mb-6">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Panel Operativo: Restaurante</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Resumen de solicitudes e inventario en tiempo real.</p>
        </div>

        {{-- SECCIÓN 1: KPIs Principales --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6 xl:grid-cols-3 2xl:gap-7.5 mb-6">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-dark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/20 mb-4">
                    <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-sm font-bold text-gray-800 dark:text-white mb-1">{{ $data['active_transfers'] }}</h4>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Transferencias en Tránsito</span>
                    </div>
                    <a href="{{ route('transfers.create') }}" class="text-sm text-brand-500 hover:underline">Pedir más</a>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-dark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-500/20 mb-4">
                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-sm font-bold text-gray-800 dark:text-white mb-1">{{ $data['active_purchases'] }}</h4>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Compras en Proceso</span>
                    </div>
                    <a href="{{ route('purchases.create') }}" class="text-sm text-blue-500 hover:underline">Solicitar</a>
                </div>
            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm dark:border-red-900/30 dark:bg-gray-dark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/20 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-sm font-bold text-red-800 dark:text-red-400 mb-1">{{ $data['low_stock_count'] }}</h4>
                        <span class="text-sm font-medium text-red-600 dark:text-red-500">Productos en Estado Crítico</span>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm text-red-600 hover:underline font-semibold">Revisar Catálogo</a>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 2: Tablas de Actividad --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 dark:text-white/90">Mis Últimas Transferencias</h3>
                    <a href="{{ route('transfers.index') }}" class="text-xs font-medium text-brand-500 hover:underline">Ver todas</a>
                </div>
                <div class="p-0">
                    @if($data['recent_transfers']->count())
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($data['recent_transfers'] as $transfer)
                                <li class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">ID #{{ $transfer->id }}</p>
                                        <p class="text-xs text-gray-500">{{ $transfer->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $transfer->status?->label() ?? 'Desconocido' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="p-6 text-sm text-gray-500 text-center">No has solicitado transferencias recientes.</p>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-red-200 bg-white dark:border-red-900/30 dark:bg-gray-dark overflow-hidden">
                <div class="border-b border-red-100 bg-red-50 px-6 py-4 dark:border-gray-800 dark:bg-red-500/10">
                    <h3 class="font-bold text-red-800 dark:text-red-400">Atención Inmediata (Stock)</h3>
                </div>
                <div class="p-0">
                    @if($data['low_stock_list']->count())
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($data['low_stock_list'] as $product)
                                <li class="flex items-center justify-between px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $product->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ $product->stock }} / {{ $product->min_stock }}</p>
                                        <p class="text-xs text-gray-500">Unidades</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="p-6 text-sm text-gray-500 text-center">Todo el inventario está en niveles saludables.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
