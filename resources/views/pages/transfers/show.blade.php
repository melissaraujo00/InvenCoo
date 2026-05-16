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
            {{-- Formulario de Aprobación envuelto en Alpine.js para controlar el Modal --}}
        <div x-data="{ showRejectModal: false }">
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
                                                   value="{{ $transfer->status === \App\Enums\StatusEnum::PENDING ? $detail->quantity_requested : $detail->quantity_sent }}"
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
                        
                        {{-- El botón ahora solo activa el modal de Alpine --}}
                        <x-form.button
                            type="button"
                            variant="danger"
                            @click="showRejectModal = true"
                        >Rechazar</x-form.button>
                    </div>
                </div>
            </form>

            {{-- Formulario oculto exclusivo para el rechazo --}}
            <form id="form-reject-{{ $transfer->id }}" action="{{ route('transfers.reject', $transfer) }}" method="POST" class="hidden">
                @csrf
                @method('PATCH')
            </form>

            {{-- Modal de Confirmación Moderno (Estilo Tailwind UI) --}}
            <div x-show="showRejectModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                {{-- Backdrop --}}
                <div x-show="showRejectModal" 
                     x-transition.opacity 
                     class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>
        
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        {{-- Panel del Modal --}}
                        <div x-show="showRejectModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             @click.away="showRejectModal = false"
                             class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 dark:bg-gray-800">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10 dark:bg-red-500/20">
                                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Rechazar Transferencia</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">¿Estás seguro de que deseas rechazar esta transferencia definitivamente? Esta acción anulará la solicitud y no se moverá ningún producto del inventario.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" 
                                        @click="document.getElementById('form-reject-{{ $transfer->id }}').submit()" 
                                        class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                                    Confirmar Rechazo
                                </button>
                                <button type="button" 
                                        @click="showRejectModal = false" 
                                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-gray-700 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            :show-actions="false"
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
