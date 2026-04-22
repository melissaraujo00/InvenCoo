@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Transferencias - Panel de Bodega" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Transferencias pendientes de envío --}}
        <div class="mb-8">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90 mb-4">📦 Transferencias por enviar</h2>
            @if($pendingShipments->count())
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destino</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha solicitud</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pendingShipments as $transfer)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-3 text-sm">{{ $transfer->id }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $transfer->destinationBranch->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $transfer->requestingUser->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $transfer->creation_date?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('transfers.show', $transfer) }}"
                                           class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
                                            Ver / Enviar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-gray-500 dark:text-gray-400">No hay transferencias pendientes de envío.</p>
                </div>
            @endif
        </div>

        {{-- Historial de envíos realizados --}}
        <div>
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90 mb-4">📜 Historial de envíos</h2>
            @if($history->count())
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destino</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha envío</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($history as $transfer)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-3 text-sm">{{ $transfer->id }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $transfer->destinationBranch->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                            @if($transfer->status == 'shipped') bg-purple-100 text-purple-800 dark:bg-purple-500/20 dark:text-purple-400
                                            @elseif($transfer->status == 'received') bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400
                                            @elseif($transfer->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ $transfer->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $transfer->shipping_date?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                    <p class="text-gray-500 dark:text-gray-400">No hay envíos realizados aún.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
