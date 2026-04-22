@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Solicitar Transferencia" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <form action="{{ route('transfers.store') }}" method="POST" x-data="transferForm()">
            @csrf

            {{-- Tarjeta de información general --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Origen (solo lectura) --}}
                    <x-form.input
                        label="Sucursal origen"
                        :value="$originOffices->first()->name ?? 'N/A'"
                        disabled
                        readonly
                    />

                    {{-- Destino --}}
                    <x-form.select
                        name="destination_branch"
                        label="Sucursal destino"
                        :options="$destinationOffices->pluck('name', 'id')->toArray()"
                        placeholder="Seleccione una sucursal"
                        required
                    />
                </div>

               
            </div>

            {{-- Tarjeta de productos --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Productos a transferir</h3>
                    <x-form.button
                        type="button"
                        variant="secondary"
                        size="sm"
                        x-on:click="addProduct()"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Agregar producto
                    </x-form.button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-2 py-2 text-left text-sm font-medium text-gray-500">Producto</th>
                                <th class="px-2 py-2 text-left text-sm font-medium text-gray-500">Cantidad solicitada</th>
                                <th class="px-2 py-2 text-center text-sm font-medium text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in products" :key="index">
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-2 py-2">
                                        <select
                                            :name="'products['+index+'][product_id]'"
                                            x-model="item.product_id"
                                            class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                            required
                                        >
                                            <option value="">Seleccione producto</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->name }} (stock disponible: {{ $product->available_stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-2">
                                        <input
                                            type="number"
                                            step="1"
                                            min="1"
                                            :name="'products['+index+'][quantity]'"
                                            x-model="item.quantity"
                                            class="w-28 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                            required
                                        />
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button
                                            type="button"
                                            x-on:click="removeProduct(index)"
                                            class="text-red-500 hover:text-red-700 transition-colors"
                                            title="Eliminar producto"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="products.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                    No hay productos agregados. Haz clic en "Agregar producto".
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex justify-end gap-3">
                <x-form.button type="submit" variant="primary" size="md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Enviar solicitud
                </x-form.button>
                <x-form.button href="{{ route('transfers.index') }}" variant="secondary" size="md">
                    Cancelar
                </x-form.button>
            </div>
        </form>
    </div>

    <script>
        function transferForm() {
            return {
                products: [{ product_id: '', quantity: 1 }],
                addProduct() {
                    this.products.push({ product_id: '', quantity: 1 });
                },
                removeProduct(index) {
                    if (this.products.length > 1) {
                        this.products.splice(index, 1);
                    } else {
                        alert('Debe haber al menos un producto.');
                    }
                }
            }
        }
    </script>
@endsection
