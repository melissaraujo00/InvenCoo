@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

        <div class="mb-6">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Panel de Ejecución: Bodega Central</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Tus tareas operativas autorizadas para hoy.</p>
        </div>

        {{-- SECCIÓN 1: El Objetivo del Día (KPIs) --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 2xl:gap-7.5 mb-6">

            <div class="rounded-2xl border {{ $data['pending_shipments'] > 0 ? 'border-orange-200 bg-orange-50 dark:border-orange-800/50 dark:bg-orange-900/20' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark' }} p-6 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full {{ $data['pending_shipments'] > 0 ? 'bg-orange-100 dark:bg-orange-500/20 text-orange-600' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    @if($data['pending_shipments'] > 0)
                        <span class="inline-flex rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-800 dark:bg-orange-500/20 dark:text-orange-400 animate-pulse">Acción Requerida</span>
                    @endif
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-gray-800 dark:text-white mb-1">{{ $data['pending_shipments'] }}</h4>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Paquetes por enviar</span>
                    </div>
                    <a href="{{ route('transfers.index') }}" class="text-sm font-semibold {{ $data['pending_shipments'] > 0 ? 'text-orange-600 hover:text-orange-700' : 'text-gray-500 hover:text-gray-700' }}">Ir a Despacho &rarr;</a>
                </div>
            </div>

            <div class="rounded-2xl border {{ $data['pending_purchases'] > 0 ? 'border-blue-200 bg-blue-50 dark:border-blue-800/50 dark:bg-blue-900/20' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark' }} p-6 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full {{ $data['pending_purchases'] > 0 ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-600' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    @if($data['pending_purchases'] > 0)
                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-500/20 dark:text-blue-400 animate-pulse">Autorizado</span>
                    @endif
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-gray-800 dark:text-white mb-1">{{ $data['pending_purchases'] }}</h4>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Compras listas para procesar</span>
                    </div>
                    <a href="{{ route('purchases.index') }}" class="text-sm font-semibold {{ $data['pending_purchases'] > 0 ? 'text-blue-600 hover:text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">Ir a Compras &rarr;</a>
                </div>
            </div>

        </div>

        {{-- SECCIÓN 2: Listas Rápidas de Tareas --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h3 class="font-bold text-gray-800 dark:text-white/90">Próximos Despachos (Prioridad)</h3>
                </div>
                <div class="p-0">
                    @if($data['shipments_list']->count())
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($data['shipments_list'] as $transfer)
                                <li class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Destino: {{ $transfer->destinationBranch->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">Autorizado {{ $transfer->updated_at->diffForHumans() }}</p>
                                    </div>
                                    <a href="{{ route('transfers.show', $transfer) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Preparar</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="p-6 text-sm text-gray-500 text-center">No hay despachos urgentes en cola.</p>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h3 class="font-bold text-gray-800 dark:text-white/90">Próximas Compras (Prioridad)</h3>
                </div>
                <div class="p-0">
                    @if($data['purchases_list']->count())
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($data['purchases_list'] as $purchase)
                                <li class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Requisición #{{ $purchase->id }}</p>
                                        <p class="text-xs text-gray-500">Solicitado por {{ $purchase->requestingUser->name ?? 'N/A' }}</p>
                                    </div>
                                    <a href="{{ route('purchases.show', $purchase) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Ver Lista</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="p-6 text-sm text-gray-500 text-center">No hay compras pendientes autorizadas.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
