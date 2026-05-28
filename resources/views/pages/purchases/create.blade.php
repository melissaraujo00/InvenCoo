@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Solicitar Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <form action="{{ route('purchases.store') }}" method="POST" x-data="purchaseForm()" @submit="isSubmitting = true">
            @csrf

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white/90">Información General</h3>
                <div class="w-full">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Notas o Justificación (Opcional)
                    </label>
                    <textarea
                        name="note"
                        rows="3"
                        placeholder="Ej. Se necesita con urgencia para el evento del sábado..."
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-gray-800 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    ></textarea>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Productos a Comprar</h3>
                    <x-form.button type="button" variant="secondary" size="sm" @click="addProduct()">
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
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400 w-2/3">Producto</th>
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400 text-center w-1/4">Cantidad</th>
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400 text-center">Remover</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in products" :key="index">
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                    <td class="px-3 py-3">
                                        <select
                                            :name="'products['+index+'][product_id]'"
                                            x-model="item.product_id"
                                            class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                            required
                                        >
                                            <option value="">Seleccione un producto...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-3">
                                        <input type="number" step="1" min="1" :name="'products['+index+'][quantity]'" x-model.number="item.quantity" class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-center text-sm font-bold focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required />
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <button type="button" @click="removeProduct(index)" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 p-2 rounded-lg">
                                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 p-4 lg:left-72">
                <div class="mx-auto max-w-screen-2xl flex justify-end gap-4">
                    <x-form.button href="{{ route('purchases.index') }}" variant="secondary" size="lg" x-bind:disabled="isSubmitting">Cancelar</x-form.button>
                    <x-form.button type="submit" variant="primary" size="lg" x-bind:disabled="isSubmitting" x-bind:class="{ 'opacity-70 cursor-not-allowed': isSubmitting }">
                        <span x-show="!isSubmitting">Enviar Solicitud</span>
                        <span x-show="isSubmitting" style="display: none;">Procesando...</span>
                    </x-form.button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchaseForm', () => ({
                isSubmitting: false,
                products: [{ product_id: '', quantity: 1 }],
                addProduct() { this.products.push({ product_id: '', quantity: 1 }); },
                removeProduct(index) {
                    if (this.products.length > 1) { this.products.splice(index, 1); }
                    else { alert('Debe solicitar al menos un producto.'); }
                }
            }));
        });
    </script>
@endsection
