@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detalle de Transferencia #{{ $transfer->id }}" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Transferencia #{{ $transfer->id }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Estado: <span class="font-semibold">{{ $transfer->status->label() }}</span>
                </p>
            </div>
            <x-form.button href="{{ route('transfers.index') }}" variant="secondary">
                Volver
            </x-form.button>
        </div>

        {{-- Tarjetas de Origen y Destino --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <x-common.component-card title="Origen" class="p-4 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-gray-700 dark:text-gray-300">
                    {{ $transfer->originatingBranch->name ?? 'N/A' }}
                </p>
            </x-common.component-card>
            <x-common.component-card title="Destino" class="p-4 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-gray-700 dark:text-gray-300">
                    {{ $transfer->destinationBranch->name ?? 'N/A' }}
                </p>
            </x-common.component-card>
        </div>

        {{-- Formulario de Aprobación (si aplica) --}}
        @if($canApprove)
            <form action="{{ route('transfers.approve', $transfer) }}" method="POST" class="mb-6">
                @csrf
                @method('PATCH')
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white/90">
                        Aprobar / Rechazar transferencia
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-2 text-gray-500 dark:text-gray-400">Producto</th>
                                    <th class="text-left py-2 text-gray-500 dark:text-gray-400">Solicitado</th>
                                    <th class="text-left py-2 text-gray-500 dark:text-gray-400">Aprobar cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfer->details as $detail)
                                    <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                        <td class="py-2 text-gray-700 dark:text-gray-300">{{ $detail->product->name }}</td>
                                        <td class="py-2 text-gray-700 dark:text-gray-300">{{ $detail->quantity_requested }}</td>
                                        <td class="py-2">
                                            <input type="number" name="details[{{ $loop->index }}][id]" value="{{ $detail->id }}" hidden>
                                            <input type="number"
                                                   name="details[{{ $loop->index }}][quantity_sent]"
                                                   value="{{ $detail->quantity_sent ?? 0 }}"
                                                   class="w-24 rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                   min="0"
                                                   max="{{ $detail->quantity_requested }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex gap-3">
                        <x-form.button type="submit" variant="primary">Aprobar</x-form.button>
                        <x-form.button
                            href="{{ route('transfers.reject', $transfer) }}"
                            variant="danger"
                            onclick="return confirm('¿Rechazar transferencia?')"
                        >Rechazar</x-form.button>
                    </div>
                </div>
            </form>
        @endif

        {{-- Formulario de Envío (si aplica) --}}
        @if($canShip)
            <form action="{{ route('transfers.ship', $transfer) }}" method="POST" class="mb-6">
                @csrf
                @method('PATCH')
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white/90">
                        Confirmar envío
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">Productos aprobados:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1 text-gray-700 dark:text-gray-300">
                        @foreach($transfer->details as $detail)
                            <li>{{ $detail->product->name }}: {{ $detail->quantity_sent }} unidades</li>
                        @endforeach
                    </ul>
                    <x-form.button type="submit" variant="primary" class="mt-4">Marcar como enviado</x-form.button>
                </div>
            </form>
        @endif

        {{-- Formulario de Recepción (si aplica) --}}
        @if($canReceive)
            <form action="{{ route('transfers.receive', $transfer) }}" method="POST" class="mb-6">
                @csrf
                @method('PATCH')
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white/90">
                        Confirmar recepción
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">Productos enviados:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1 text-gray-700 dark:text-gray-300">
                        @foreach($transfer->details as $detail)
                            <li>{{ $detail->product->name }}: {{ $detail->quantity_sent }} unidades</li>
                        @endforeach
                    </ul>
                    <x-form.button type="submit" variant="primary" class="mt-4">Recibir en destino</x-form.button>
                </div>
            </form>
        @endif

        {{-- Tabla de detalles (reutilizando x-tables.table) --}}
        <x-tables.table
            title="Detalle de productos"
            :headers="['Producto', 'Solicitado', 'Aprobado/Enviado']"
            emptyMessage="Sin productos"
        >
            @foreach($transfer->details as $detail)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $detail->product->name }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $detail->quantity_requested }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $detail->quantity_sent ?? '—' }}
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection
