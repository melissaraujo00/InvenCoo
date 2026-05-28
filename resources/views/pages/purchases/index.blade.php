@extends('layouts.app')
@php use App\Enums\StatusEnum; @endphp
@section('content')
    <x-common.page-breadcrumb pageTitle="Solicitudes de Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Historial de Solicitudes</h2>
                <x-form.button href="{{ route('purchases.create') }}" variant="primary" size="md">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Solicitud
                </x-form.button>
        </div>

        <x-tables.table
            title="Solicitudes"
            :headers="['ID', 'Solicitante', 'Fecha solicitud', 'Estado']"
            :paginator="$requests"
            emptyMessage="No hay solicitudes registradas"
        >
            @foreach($requests as $request)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        #{{ $request->id }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $request->requestingUser->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $request->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                            @if($request->status == StatusEnum::PENDING)
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400
                            @elseif($request->status == StatusEnum::APPROVED)
                                bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400
                            @elseif($request->status == StatusEnum::REJECTED)
                                bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400
                            @elseif($request->status == StatusEnum::PARTIALLY_APPROVED)
                                bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400
                            @else
                                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                            @endif">
                           {{ $request->status?->label() ?? 'Corrupto' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('purchases.show', $request) }}"
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
