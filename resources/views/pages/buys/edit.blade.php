@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Editar Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                Editar Compra #{{ $buy->id }}
            </h2>
            <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="md">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </x-form.button>
        </div>

        <form action="{{ route('buys.update', $buy->id) }}" method="POST" x-data="productForm()">
            @csrf
            @method('PUT')

            {{-- Cabecera --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.select name="supplier_id" label="Proveedor" :options="$suppliers->pluck('company_name', 'id')->toArray()" :value="old('supplier_id', $buy->supplier_id)" placeholder="Seleccione proveedor" />
                    <x-form.select name="office_id" label="Oficina" :options="$offices->pluck('name', 'id')->toArray()" :value="old('office_id', $buy->office_id)" placeholder="Seleccione oficina" />
                    <x-form.input type="date" name="date" label="Fecha de compra" :value="old('date', $buy->date)" required />

                    {{-- Campo descuento con x-model --}}
                    <div>
                        <x-form.input
                            type="number"
                            step="0.01"
                            name="discount"
                            label="Descuento ($)"
                            x-model="discount"
                            :value="old('discount', $buy->discount)"
                            helper="Aplica a toda la compra"
                        />
                    </div>
                </div>
            </div>

            {{-- Productos --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Productos</h3>
                    <x-form.button type="button" variant="secondary" size="sm" x-on:click="addProduct()">
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
                                <th class="px-2 py-2 text-left text-sm font-medium text-gray-500">Cantidad</th>
                                <th class="px-2 py-2 text-left text-sm font-medium text-gray-500">Precio unitario</th>
                                <th class="px-2 py-2 text-left text-sm font-medium text-gray-500">Subtotal</th>
                                <th class="px-2 py-2 text-center text-sm font-medium text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(product, index) in products" :key="index">
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-2 py-2">
                                        <select :name="'products['+index+'][id]'" x-model="product.id" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                            <option value="">Seleccione producto</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->name }} ({{ $product->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" step="1" min="1" :name="'products['+index+'][quantity]'" x-model="product.quantity" class="w-24 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" step="0.01" min="0" :name="'products['+index+'][price]'" x-model="product.price" class="w-32 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    </td>
                                    <td class="px-2 py-2 text-right font-medium" x-text="formatCurrency(product.quantity * product.price)"></td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" x-on:click="removeProduct(index)" class="text-red-500 hover:text-red-700" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <td colspan="3" class="px-2 py-3 text-right font-semibold">Subtotal:</td>
                                <td class="px-2 py-3 text-right font-semibold" x-text="formatCurrency(subtotal)"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-2 py-2 text-right">Descuento:</td>
                                <td class="px-2 py-2 text-right" x-text="formatCurrency(discount)"></td>
                                <td></td>
                            </tr>
                            <tr class="text-lg font-bold">
                                <td colspan="3" class="px-2 py-3 text-right">Total:</td>
                                <td class="px-2 py-3 text-right text-brand-600" x-text="formatCurrency(total)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <x-form.button type="submit" variant="primary" size="md">Actualizar compra</x-form.button>
                <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="md">Cancelar</x-form.button>
            </div>
        </form>
    </div>

    <script>
        function productForm() {
            // Cargar productos existentes desde el backend
            let initialProducts = {!! json_encode($buy->details->map(function($detail) {
                return [
                    'id' => $detail->product_id,
                    'quantity' => (int) $detail->quantity,
                    'price' => (float) $detail->price,
                ];
            })) !!};

            return {
                products: initialProducts.length ? initialProducts : [{ id: '', quantity: 1, price: 0 }],
                discount: {{ floatval(old('discount', $buy->discount)) }},

                get subtotal() {
                    return this.products.reduce((sum, p) => sum + (p.quantity * p.price), 0);
                },
                get total() {
                    return this.subtotal - this.discount;
                },
                addProduct() {
                    this.products.push({ id: '', quantity: 1, price: 0 });
                },
                removeProduct(index) {
                    if (this.products.length > 1) {
                        this.products.splice(index, 1);
                    } else {
                        alert('Debe haber al menos un producto');
                    }
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 2 }).format(value || 0);
                }
            }
        }
    </script>
@endsection
