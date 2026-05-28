@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

        <div class="mb-6">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Panel de Control: Administración Global</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Atención requerida: Autorizaciones pendientes que bloquean la operación.</p>
        </div>

        {{-- SECCIÓN 1: KPIs de Cuello de Botella --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6 2xl:gap-7.5 mb-6">

            <!-- KPI 1: Transferencias por Aprobar -->
            <div class="rounded-2xl border {{ $data['pending_transfers_count'] > 0 ? 'border-yellow-400 dark:border-yellow-500' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-900 p-6 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full {{ $data['pending_transfers_count'] > 0 ? 'bg-yellow-100 text-yellow-600 dark:bg-gray-800 dark:text-yellow-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    @if($data['pending_transfers_count'] > 0)
                        <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Requiere Revisión</span>
                    @endif
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-gray-800 dark:text-white mb-1">{{ $data['pending_transfers_count'] }}</h4>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Transferencias Pendientes</span>
                    </div>
                    <a href="{{ route('transfers.index') }}" class="text-sm font-semibold {{ $data['pending_transfers_count'] > 0 ? 'text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300' : 'text-gray-500 hover:text-gray-700' }}">Ver Todas &rarr;</a>
                </div>
            </div>

            <!-- KPI 2: Compras por Aprobar -->
            <div class="rounded-2xl border {{ $data['pending_purchases_count'] > 0 ? 'border-yellow-400 dark:border-yellow-500' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-900 p-6 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full {{ $data['pending_purchases_count'] > 0 ? 'bg-yellow-100 text-yellow-600 dark:bg-gray-800 dark:text-yellow-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    @if($data['pending_purchases_count'] > 0)
                        <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Requiere Revisión</span>
                    @endif
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-gray-800 dark:text-white mb-1">{{ $data['pending_purchases_count'] }}</h4>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Compras Pendientes</span>
                    </div>
                    <a href="{{ route('purchases.index') }}" class="text-sm font-semibold {{ $data['pending_purchases_count'] > 0 ? 'text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300' : 'text-gray-500 hover:text-gray-700' }}">Ver Todas &rarr;</a>
                </div>
            </div>

            <!-- KPI 3: Salud del Inventario -->
            <div class="rounded-2xl border {{ $data['low_stock_count'] > 0 ? 'border-red-200 bg-red-50 dark:border-red-900/30 dark:bg-gray-dark' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark' }} p-6 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full {{ $data['low_stock_count'] > 0 ? 'bg-red-100 dark:bg-red-500/20 text-red-600' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold {{ $data['low_stock_count'] > 0 ? 'text-red-800 dark:text-red-400' : 'text-gray-800 dark:text-white' }} mb-1">{{ $data['low_stock_count'] }}</h4>
                        <span class="text-sm font-medium {{ $data['low_stock_count'] > 0 ? 'text-red-600 dark:text-red-500' : 'text-gray-500 dark:text-gray-400' }}">Alertas de Stock Mínimo</span>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold {{ $data['low_stock_count'] > 0 ? 'text-red-600 hover:text-red-700' : 'text-gray-500 hover:text-gray-700' }}">Inventario &rarr;</a>
                </div>
            </div>

        </div>

        {{-- SECCIÓN 2: Acciones Directas (Atender lo más antiguo primero) --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">

            <!-- Transferencias Bloqueadas -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h3 class="font-bold text-gray-800 dark:text-white/90">Transferencias Esperando Aprobación</h3>
                </div>
                <div class="p-0">
                    @if($data['pending_transfers_list']->count())
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($data['pending_transfers_list'] as $transfer)
                                <li class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">De: {{ $transfer->requestingUser->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">Esperando hace {{ $transfer->created_at->diffForHumans() }}</p>
                                    </div>
                                    <a href="{{ route('transfers.show', $transfer) }}" class="rounded-lg bg-yellow-100 px-3 py-1.5 text-xs font-bold text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-400 dark:hover:bg-yellow-500/30 transition-colors">Revisar</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="p-6 text-sm text-gray-500 text-center">No hay transferencias pendientes. Operación fluida.</p>
                    @endif
                </div>
            </div>

            <!-- Compras Bloqueadas -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h3 class="font-bold text-gray-800 dark:text-white/90">Compras Esperando Aprobación</h3>
                </div>
                <div class="p-0">
                    @if($data['pending_purchases_list']->count())
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($data['pending_purchases_list'] as $purchase)
                                <li class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">De: {{ $purchase->requestingUser->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">Esperando hace {{ $purchase->created_at->diffForHumans() }}</p>
                                    </div>
                                    <a href="{{ route('purchases.show', $purchase) }}" class="rounded-lg bg-yellow-100 px-3 py-1.5 text-xs font-bold text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-400 dark:hover:bg-yellow-500/30 transition-colors">Revisar</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="p-6 text-sm text-gray-500 text-center">No hay compras pendientes. Presupuesto al día.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
