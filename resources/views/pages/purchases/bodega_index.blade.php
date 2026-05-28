@extends('layouts.app')
@php use App\Enums\StatusEnum; @endphp
@section('content')
    <x-common.page-breadcrumb pageTitle="Compras - Panel de Bodega" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

        {{-- SECCIÓN 1: Tareas Pendientes (Lo que Bodega debe comprar HOY) --}}
        <div class="mb-8">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90 mb-4">🛒 Compras Autorizadas (Pendientes de Procesar)</h2>
            @if($pendingPurchases->count())
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-blue-50 dark:bg-blue-900/20">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 dark:text-blue-300 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 dark:text-blue-300 uppercase">Solicitante</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 dark:text-blue-300 uppercase">Autorizado Por</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 dark:text-blue-300 uppercase">Fecha Solicitud</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 dark:text-blue-300 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pendingPurchases as $purchase)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-white">#{{ $purchase->id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $purchase->requestingUser->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $purchase->authorizingUser->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $purchase->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('purchases.show', $purchase) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                            </svg>
                                            Procesar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">No hay compras autorizadas pendientes de procesar. ¡Buen trabajo!</p>
                </div>
            @endif
        </div>

        {{-- SECCIÓN 2: Historial (Lo que ya procesaron) --}}
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90 mb-4">📜 Historial de Compras Realizadas</h2>
            @if($history->count())
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Actualización</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ver</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($history as $purchase)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-white">#{{ $purchase->id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $purchase->requestingUser->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">
                                            {{ $purchase->status?->label() ?? 'Procesada' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $purchase->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-gray-500 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $history->links() }}
                </div>
            @else
                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-gray-500 dark:text-gray-400">El historial de compras procesadas está vacío.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
