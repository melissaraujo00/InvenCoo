@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Movimientos" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Listado de Movimientos
            </h2>
            <x-form.button href="{{ route('movements.create') }}" variant="primary" size="md">
                Nuevo Ajuste Manual
            </x-form.button>
        </div>

        <x-tables.table
            title="Movimientos de Inventario"
            :headers="['ID', 'Transacción', 'Fecha', 'Oficina', 'Tipo', 'Entrada/Salida', 'Cantidad Total', 'Usuario', 'Descripción']"
            :paginator="$movements"
            :searchable="true"
            emptyMessage="No hay movimientos registrados"
        >
            {{-- Slot de filtros adicionales --}}
            <x-slot name="filters">
                <form method="GET" class="flex flex-wrap gap-2 items-end">
                    <x-form.select
                        name="type_id"
                        label="Tipo"
                        :options="$types->pluck('name', 'id')->toArray()"
                        :value="request('type_id')"
                        placeholder="Todos los tipos"
                        containerClass="w-48"
                        no-label
                    />
                    <x-form.select
                        name="input_type"
                        label="Entrada/Salida"
                        :options="['E' => 'Entrada', 'S' => 'Salida']"
                        :value="request('input_type')"
                        placeholder="Todos"
                        containerClass="w-40"
                        no-label
                    />
                    <x-form.button type="submit" variant="primary" size="sm">
                        Filtrar
                    </x-form.button>
                    @if(request()->anyFilled(['type_id', 'input_type']))
                        <x-form.button href="{{ route('movements.index') }}" variant="secondary" size="sm">
                            Limpiar
                        </x-form.button>
                    @endif
                </form>
            </x-slot>

            {{-- Filas de la tabla --}}
            @foreach($movements as $movement)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $movement->id }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $movement->transaction_id }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $movement->date_movement }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $movement->office->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $movement->type->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($movement->input_type == 'E')
                            <span class="text-green-600 dark:text-green-400">Entrada</span>
                        @else
                            <span class="text-red-600 dark:text-red-400">Salida</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $movement->total_quantity }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ $movement->user->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ Str::limit($movement->description, 40) }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">
                            <a href="{{ route('movements.show', $movement->id) }}"
                               class="hover:text-blue-500 transition-colors"
                               title="Ver detalle">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection
