@extends('layouts.app')
@php use App\Enums\StatusEnum; @endphp

@section('content')
    <x-common.page-breadcrumb pageTitle="Compras - Panel de Bodega" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

        {{-- SECCIÓN 1: Tareas Pendientes (Lo que Bodega debe comprar HOY) --}}
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Compras Autorizadas (Pendientes de Procesar)</h2>
            </div>

            <x-tables.table
                title=""
                :headers="['ID', 'Solicitante', 'Autorizado Por', 'Fecha Solicitud', 'Acción']"
                :show-actions="false"
                emptyMessage="No hay compras autorizadas pendientes de procesar. ¡Buen trabajo!"
            >
                @foreach($pendingPurchases as $purchase)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                            #{{ $purchase->id }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $purchase->requestingUser->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $purchase->authorizingUser->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $purchase->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            {{-- Corrección de ruta: purchases -> buys --}}
                            <x-form.button href="{{ route('buys.show', $purchase) }}" variant="primary" size="sm">
                                Procesar
                            </x-form.button>
                        </td>
                    </tr>
                @endforeach
            </x-tables.table>
        </div>

        {{-- SECCIÓN 2: Historial (Lo que ya procesaron) --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Historial de Compras Realizadas</h2>
            </div>

            <x-tables.table
                title=""
                :headers="['ID', 'Solicitante', 'Estado', 'Fecha Actualización', 'Ver']"
                :show-actions="false"
                :paginator="$history"
                emptyMessage="El historial de compras procesadas está vacío."
            >
                @foreach($history as $purchase)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                            #{{ $purchase->id }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $purchase->requestingUser->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold shadow-sm bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400 border border-green-200 dark:border-green-800/50">
                                {{ $purchase->status?->label() ?? 'Procesada' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $purchase->updated_at?->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            {{-- Corrección de ruta: purchases -> buys --}}
                            <x-form.button href="{{ route('buys.show', $purchase) }}" variant="secondary" size="sm">
                                Ver Detalle
                            </x-form.button>
                        </td>
                    </tr>
                @endforeach
            </x-tables.table>
        </div>
    </div>
@endsection