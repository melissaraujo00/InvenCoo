@extends('layouts.app')
@php use App\Enums\StatusEnum; @endphp
@section('content')
    <x-common.page-breadcrumb pageTitle="Transferencias" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Listado de Transferencias</h2>
            @role('Administrador Restaurante')
                <x-form.button href="{{ route('transfers.create') }}" variant="primary" size="md">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Solicitud
                </x-form.button>
            @endrole
        </div>

        <x-tables.table
            title="Transferencias"
            :headers="['ID', 'Origen', 'Destino', 'Fecha solicitud', 'Estado']"
            :paginator="$transfers"
            emptyMessage="No hay transferencias"
        >
            @foreach($transfers as $transfer)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    {{-- ID --}}
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $transfer->id }}
                    </td>

                    {{-- Origen --}}
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $transfer->originatingBranch->name ?? 'N/A' }}
                    </td>

                    {{-- Destino --}}
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $transfer->destinationBranch->name ?? 'N/A' }}
                    </td>

                    {{-- Fecha solicitud --}}
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $transfer->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                    </td>

                    {{-- Estado (badge con modo oscuro) --}}
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                            @if($transfer->status == 'pending')
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400
                            @elseif($transfer->status == 'approved')
                                bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400
                            @elseif($transfer->status == 'partially_approved')
                                bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400
                            @elseif($transfer->status == 'rejected')
                                bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400
                            @elseif($transfer->status == 'shipped')
                                bg-purple-100 text-purple-800 dark:bg-purple-500/20 dark:text-purple-400
                            @else
                                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                            @endif">
                            {{ $transfer->status->label() ?? $transfer->status }}
                        </span>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('transfers.show', $transfer) }}"
                               class="text-gray-500 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                               title="Ver">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection
