@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Solicitar Transferencia" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Envolvemos todo el formulario en el estado reactivo de Alpine --}}
        <form action="{{ route('transfers.store') }}" method="POST" x-data="transferForm()" @submit="isSubmitting = true">
            @csrf

            {{-- Tarjeta de información general --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Origen fijo (Cooperativa) --}}
                    <x-form.input
                        label="Sucursal Origen (Despacha)"
                        :value="$sourceOffice->name"
                        disabled
                        readonly
                        class="bg-gray-50 dark:bg-gray-900 cursor-not-allowed font-bold text-gray-700"
                    />

                    {{-- Destino dinámico (Oficina del usuario solicitante) --}}
                    <x-form.input
                        label="Sucursal Destino (Recibe)"
                        :value="$destinationOffice->name"
                        disabled
                        readonly
                        class="bg-brand-50 dark:bg-brand-900/20 cursor-not-allowed font-bold text-brand-700 dark:text-brand-400"
                    />
                </div>
            </div>

            {{-- Tarjeta de productos (Motor Dinámico) --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Productos a Requerir</h3>
                    <x-form.button
                        type="button"
                        variant="secondary"
                        size="sm"
                        @click="addProduct()"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Añadir Fila
                    </x-form.button>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400 w-2/3">Catálogo de Productos</th>
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400 text-center w-1/4">Cantidad</th>
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400 text-center">Remover</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in products" :key="index">
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-3 py-3">
                                        <select
                                            :name="'products['+index+'][product_id]'"
                                            x-model="item.product_id"
                                            class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition-all"
                                            required
                                        >
                                            <option value="">Seleccione un producto...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->code ?? 'S/C' }} - {{ $product->name }} (Stock en Cooperativa: {{ $product->available_stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-3">
                                        <input
                                            type="number"
                                            step="1"
                                            min="1"
                                            :name="'products['+index+'][quantity]'"
                                            x-model.number="item.quantity"
                                            class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-center text-sm font-bold focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition-all"
                                            required
                                        />
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <button
                                            type="button"
                                            @click="removeProduct(index)"
                                            class="text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 p-2 rounded-lg transition-colors"
                                            title="Eliminar línea"
                                        >
                                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Estado vacío --}}
                <div x-show="products.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg mt-4">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Sin productos</h3>
                    <p class="mt-1 text-sm text-gray-500">Agrega al menos una línea para procesar la transferencia.</p>
                </div>
            </div>

            {{-- Botones de acción con bloqueo reactivo --}}
            <div class="fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 p-4 lg:left-72">
                <div class="mx-auto max-w-screen-2xl flex justify-end gap-4">
                    <x-form.button href="{{ route('transfers.index') }}" variant="secondary" size="lg" x-bind:disabled="isSubmitting">
                        Cancelar
                    </x-form.button>
                    
                    <x-form.button type="submit" variant="primary" size="lg" x-bind:disabled="isSubmitting" x-bind:class="{ 'opacity-70 cursor-not-allowed': isSubmitting }">
                        {{-- Estado normal --}}
                        <span x-show="!isSubmitting" class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Enviar Solicitud
                        </span>
                        {{-- Estado de carga --}}
                        <span x-show="isSubmitting" class="flex items-center" style="display: none;">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Procesando Orden...
                        </span>
                    </x-form.button>
                </div>
            </div>
        </form>
    </div>

    {{-- Lógica estricta de Alpine.js --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('transferForm', () => ({
                isSubmitting: false,
                products: [{ product_id: '', quantity: 1 }],
                
                addProduct() {
                    this.products.push({ product_id: '', quantity: 1 });
                },
                
                removeProduct(index) {
                    if (this.products.length > 1) {
                        this.products.splice(index, 1);
                    } else {
                        // En lugar de un alert feo, forzamos la alerta nativa del navegador o limpiamos el campo
                        alert('El documento no puede quedar vacío. Debe solicitar al menos un producto.');
                    }
                }
            }));
        });
    </script>
@endsection