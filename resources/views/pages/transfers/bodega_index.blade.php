@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Transferencias - Panel de Bodega" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        
        {{-- Sección 1: Transferencias pendientes de envío (Usa $pendingShipments) --}}
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Transferencias por enviar</h2>
            </div>

            <x-tables.table
                title=""
                :headers="['ID', 'Destino', 'Solicitante', 'Fecha solicitud', 'Acciones']"
                :show-actions="false"
                emptyMessage="No hay transferencias pendientes de envío en este momento."
            >
                @foreach($pendingShipments as $transfer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                            #{{ $transfer->id }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $transfer->destinationBranch->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $transfer->requestingUser->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $transfer->creation_date?->format('d/m/Y H:i') ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <x-form.button href="{{ route('transfers.show', $transfer) }}" variant="primary" size="sm">
                                Ver y Preparar
                            </x-form.button>
                        </td>
                    </tr>
                @endforeach
            </x-tables.table>
        </div>

        {{-- Sección 2: Historial de envíos realizados (Usa $transfers y Paginador) --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Historial de envíos</h2>
            </div>

            <x-tables.table
                title=""
                :headers="['ID', 'Destino', 'Estado', 'Fecha envío', 'Detalle']"
                :show-actions="false"
                :paginator="$transfers"
                emptyMessage="El historial de despachos está vacío."
            >
                @foreach($transfers as $transfer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                            #{{ $transfer->id }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $transfer->destinationBranch->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold shadow-sm
                                @if(is_object($transfer->status) ? $transfer->status->value === 'shipped' : $transfer->status === 'shipped') 
                                    bg-purple-100 text-purple-800 dark:bg-purple-500/20 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50
                                @elseif(is_object($transfer->status) ? $transfer->status->value === 'received' : $transfer->status === 'received') 
                                    bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400 border border-green-200 dark:border-green-800/50
                                @elseif(is_object($transfer->status) ? $transfer->status->value === 'rejected' : $transfer->status === 'rejected') 
                                    bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400 border border-red-200 dark:border-red-800/50
                                @else 
                                    bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600
                                @endif">
                                {{ method_exists($transfer->status, 'label') ? $transfer->status->label() : (is_object($transfer->status) ? $transfer->status->value : $transfer->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $transfer->shipping_date?->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <x-form.button href="{{ route('transfers.show', $transfer) }}" variant="secondary" size="sm">
                                Auditoría
                            </x-form.button>
                        </td>
                    </tr>
                @endforeach
            </x-tables.table>
        </div>
    </div>
@endsection