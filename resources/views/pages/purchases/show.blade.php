@extends('layouts.app')
@php use App\Enums\StatusEnum; @endphp
@section('content')
    <x-common.page-breadcrumb pageTitle="Detalle de Solicitud #{{ $purchaseRequest->id }}" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Solicitud de Compra #{{ $purchaseRequest->id }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Estado: <span class="font-semibold">{{ $purchaseRequest->status?->label() ?? 'Error de datos (Corrupto)' }}</span>
                </p>
            </div>
            <x-form.button href="{{ route('purchases.index') }}" variant="secondary">Volver</x-form.button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-common.component-card title="Solicitante" class="p-4 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-gray-700 dark:text-gray-300">{{ $purchaseRequest->requestingUser->name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $purchaseRequest->created_at?->format('d/m/Y H:i') }}</p>
            </x-common.component-card>

            <x-common.component-card title="Autorizador" class="p-4 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-gray-700 dark:text-gray-300">{{ $purchaseRequest->authorizingUser->name ?? 'Pendiente' }}</p>
            </x-common.component-card>

            <x-common.component-card title="Notas" class="p-4 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-gray-700 dark:text-gray-300 italic">{{ $purchaseRequest->note ?? 'Sin notas adicionales.' }}</p>
            </x-common.component-card>
        </div>

        {{-- Controles de Estado (Aprobar/Rechazar) --}}
        @if($purchaseRequest->status === StatusEnum::PENDING)
            @can('approve', $purchaseRequest)
            <div x-data="{ showRejectModal: false }" class="mb-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 flex gap-4">
                    <form action="{{ route('purchases.approve', $purchaseRequest) }}" method="POST">
                        @csrf @method('PATCH')
                        <x-form.button type="submit" variant="primary">Aprobar Solicitud</x-form.button>
                    </form>

                    <x-form.button type="button" variant="danger" @click="showRejectModal = true">Rechazar Solicitud</x-form.button>
                </div>

                {{-- Modal de Rechazo --}}
                <div x-show="showRejectModal" class="relative z-50" style="display: none;">
                    <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center">
                            <div @click.away="showRejectModal = false" class="relative transform rounded-2xl bg-white text-left shadow-xl sm:max-w-lg dark:bg-gray-800">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rechazar Solicitud de Compra</h3>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">¿Estás seguro de que deseas rechazar esta solicitud? Esta acción no se puede deshacer.</p>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse dark:bg-gray-800/50 rounded-b-2xl">
                                    <form action="{{ route('purchases.reject', $purchaseRequest) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Confirmar Rechazo</button>
                                    </form>
                                    <button @click="showRejectModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-gray-700 dark:text-gray-200 dark:ring-gray-600">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        @endif

        {{-- Controles de Estado (Procesar Compra) --}}
        @if($purchaseRequest->status === StatusEnum::APPROVED)
            @can('process', $purchaseRequest)
            <div class="mb-6">
                <form action="{{ route('purchases.process', $purchaseRequest) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-800/50 dark:bg-blue-900/20">
                        <h3 class="text-lg font-semibold mb-2 text-blue-800 dark:text-blue-300">Solicitud Aprobada</h3>
                        <p class="text-blue-700 dark:text-blue-400 mb-4">La compra ha sido autorizada. Una vez que hayas gestionado la compra con el proveedor, marca este documento como procesado.</p>
                        <x-form.button type="submit" variant="primary">Marcar como Compra Realizada</x-form.button>
                    </div>
                </form>
            </div>
            @endcan
        @endif

        <x-tables.table
            title="Detalle de la Requisición"
            :headers="['Producto', 'Cantidad Solicitada']"
            :show-actions="false"
            emptyMessage="Sin productos"
        >
            @foreach($purchaseRequest->details as $detail)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $detail->product->name }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-semibold">
                        {{ $detail->quantity }}
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection
