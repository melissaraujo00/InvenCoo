@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Compras" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Listado de Compras
            </h2>
            <x-form.button href="{{ route('buys.create') }}" variant="primary" size="md">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Compra
            </x-form.button>
        </div>

        <x-tables.table
            title="Compras registradas"
            :headers="['ID', 'Fecha', 'Proveedor', 'Total', 'Usuario', 'Oficina', 'Estado']"
            :paginator="$buys"
            emptyMessage="No hay compras registradas"
        >
            @foreach($buys as $buy)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $buy->id }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $buy->date }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $buy->supplier->company_name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        ${{ number_format($buy->total, 2) }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $buy->user->name }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $buy->office->name }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($buy->is_cancelled)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">
                                Cancelada
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">
                                Activa
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">
                            {{-- Ver --}}
                            <a href="{{ route('buys.show', $buy->id) }}"
                               class="hover:text-blue-500 transition-colors"
                               title="Ver detalle">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </a>

                            {{-- Editar (solo admin y no cancelada) --}}
                            @role('Administrador')
                                @if(!$buy->is_cancelled)
                                    <a href="{{ route('buys.edit', $buy->id) }}"
                                       class="hover:text-yellow-500 transition-colors"
                                       title="Editar compra">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                @endif
                            @endrole

                            {{-- Cancelar (solo si no está cancelada) --}}
                            @if(!$buy->is_cancelled)
                                <form action="{{ route('buys.cancel', $buy->id) }}" method="POST" onsubmit="return confirm('¿Anular esta compra? Se revertirá el stock.')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="hover:text-red-500 transition-colors" title="Anular compra">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                            <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif

                            {{-- Restaurar (solo si está cancelada) --}}
                            @if($buy->is_cancelled)
                                <form action="{{ route('buys.restore', $buy->id) }}" method="POST" onsubmit="return confirm('¿Restaurar esta compra? Se volverá a sumar al stock.')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="hover:text-green-500 transition-colors" title="Restaurar compra">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection
