@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Nueva Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10" x-data="buyForm({
        productsData: {{ Js::from($products->map(fn($p) => ['id' => $p->id, 'code' => $p->code, 'name' => $p->name])) }}
    })">

        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Registrar Compra</h2>
                <p class="text-sm text-gray-500">Ingrese los suministros recibidos para la cooperativa o el restaurante.</p>
            </div>
            <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="md">
                Volver al listado
            </x-form.button>
        </div>

        <form action="{{ route('buys.store') }}" method="POST" @submit="submitForm($event)">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Encabezado --}}
                <x-common.component-card title="Datos del Documento" class="lg:col-span-2">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Fila 1: Proveedor y Fecha --}}
                        <div class="relative z-50">
                            <x-form.group name="supplier_id" label="Proveedor" :required="true">
                                <x-form.select name="supplier_id" :options="$suppliers->pluck('company_name', 'id')" :value="old('supplier_id')"
                                    placeholder="Seleccione proveedor" searchable />
                            </x-form.group>
                        </div>

                        <x-form.group name="date" label="Fecha de Emisión" :required="true">
                            <x-form.input type="date" name="date" :required="true" :value="old('date', date('Y-m-d'))" />
                        </x-form.group>

                        {{-- Fila 2: Tipo de IVA y Almacén Destino --}}
                        <div class="relative z-40">
                            <x-form.group name="document_type" label="Tipo de Documento (IVA)" :required="true">
                                <select name="document_type" x-model="document_type"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    <option value="factura">Factura de Consumidor Final (IVA Incluido)</option>
                                    <option value="credito_fiscal">Crédito Fiscal (+13% IVA)</option>
                                    <option value="exento">Sujeto Exento / Mercado (Sin IVA)</option>
                                </select>
                            </x-form.group>
                        </div>

                        <div class="relative z-40">
                            <x-form.group name="office_id" label="Oficina/Almacén de Destino" :required="true">
                                <x-form.select name="office_id" :options="$offices->pluck('name', 'id')" :value="old('office_id', auth()->user()->office_id)"
                                    placeholder="Seleccione almacén" searchable />
                            </x-form.group>
                        </div>

                    </div>
                </x-common.component-card>

                {{-- Gestión de Descuentos --}}
                <x-common.component-card title="Configuración de Descuentos">
                    <div class="p-6 space-y-6">

                        {{-- Control Segmentado 1: Modo de Descuento --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Modo de
                                Descuento</label>
                            <div
                                class="flex h-11 items-center rounded-lg border border-gray-300 bg-gray-50 p-1 dark:border-gray-700 dark:bg-gray-900/50">
                                <button type="button" @click="is_global_discount = true"
                                    :class="is_global_discount ?
                                        'bg-white shadow-sm dark:bg-gray-800 text-brand-600 dark:text-brand-400 font-bold' :
                                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:dark:text-gray-200'"
                                    class="w-1/2 h-full rounded-md text-sm transition-all">
                                    Global
                                </button>
                                <button type="button" @click="is_global_discount = false"
                                    :class="!is_global_discount ?
                                        'bg-white shadow-sm dark:bg-gray-800 text-brand-600 dark:text-brand-400 font-bold' :
                                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:dark:text-gray-200'"
                                    class="w-1/2 h-full rounded-md text-sm transition-all">
                                    Por Artículo
                                </button>
                            </div>
                            {{-- Input oculto para enviar al backend --}}
                            <input type="hidden" name="discount_type" :value="is_global_discount ? 'global' : 'item'">
                        </div>

                        {{-- Panel de Descuento Global (Aparece solo si el modo es Global) --}}
                        <div x-show="is_global_discount" x-transition class="space-y-4">

                            {{-- Control Segmentado 2: Tipo de Valor --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Aplicar
                                    como</label>
                                <div
                                    class="flex h-11 items-center rounded-lg border border-gray-300 bg-gray-50 p-1 dark:border-gray-700 dark:bg-gray-900/50">
                                    <button type="button" @click="discount_unit = 'money'"
                                        :class="discount_unit === 'money' ?
                                            'bg-white shadow-sm dark:bg-gray-800 text-brand-600 dark:text-brand-400 font-bold' :
                                            'text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:dark:text-gray-200'"
                                        class="w-1/2 h-full rounded-md text-sm transition-all">
                                        Monto ($)
                                    </button>
                                    <button type="button" @click="discount_unit = 'percent'"
                                        :class="discount_unit === 'percent' ?
                                            'bg-white shadow-sm dark:bg-gray-800 text-brand-600 dark:text-brand-400 font-bold' :
                                            'text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:dark:text-gray-200'"
                                        class="w-1/2 h-full rounded-md text-sm transition-all">
                                        Porcentaje (%)
                                    </button>
                                </div>
                            </div>

                            {{-- Input del Valor --}}
                            <x-form.group name="discount" label="Valor del Descuento">
                                <x-form.input type="number" step="0.01" x-model.number="global_discount_input"
                                    placeholder="0.00" />
                            </x-form.group>
                        </div>

                    </div>
                </x-common.component-card>
            </div>

            {{-- Detalle de Productos --}}
            <x-common.component-card title="Detalle de Productos" class="mb-6 overflow-visible">
                <div
                    class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-t-xl relative z-40">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                        <div class="lg:col-span-4 relative" @click.away="searchOpen = false">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Buscar
                                Producto</label>
                            <input type="text" x-model="searchQuery" @focus="searchOpen = true"
                                @click="searchOpen = true" placeholder="Haga clic para buscar..."
                                class="h-11 w-full rounded-lg border border-brand-300 bg-white px-4 py-2 text-sm focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition-all">
                            <div x-show="searchOpen"
                                class="absolute left-0 right-0 top-full mt-1 max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 z-50">
                                <template x-for="p in filteredSearch" :key="p.id">
                                    <div @click="selectProduct(p)"
                                        class="cursor-pointer px-4 py-3 hover:bg-brand-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors text-gray-800 dark:text-white">
                                        <div class="font-medium" x-text="p.name"></div>
                                        <div class="text-xs text-gray-500 font-mono" x-text="p.code"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="lg:col-span-2"><label
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cantidad</label><input
                                type="number" min="1" x-model.number="draft.quantity" @keyup.enter="addToCart()"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-center dark:bg-gray-900 dark:text-white">
                        </div>
                        <div class="lg:col-span-2"><label
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">$
                                Costo</label><input type="number" min="0" step="0.01"
                                x-model.number="draft.price" @keyup.enter="addToCart()"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-right dark:bg-gray-900 dark:text-white">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                                :class="is_global_discount ? 'opacity-50' : ''">$ Desc. Item</label>
                            <input type="number" min="0" step="0.01" x-model.number="draft.discount"
                                @keyup.enter="addToCart()" :disabled="is_global_discount"
                                :class="is_global_discount ? 'bg-gray-100 cursor-not-allowed dark:bg-gray-800' :
                                    'bg-white dark:bg-gray-900'"
                                class="h-11 w-full rounded-lg border border-gray-300 px-4 text-right dark:text-white">
                        </div>
                        <div class="lg:col-span-2"><button type="button" @click="addToCart()" :disabled="!draft.product"
                                class="h-11 w-full rounded-lg bg-brand-600 font-medium text-white hover:bg-brand-700 transition-colors">Insertar</button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Código</th>
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3 text-center w-24">Cant.</th>
                                <th class="px-4 py-3 text-right w-32">P. Unitario</th>
                                <th class="px-4 py-3 text-right w-32" x-show="!is_global_discount">Desc. Item</th>
                                <th class="px-4 py-3 text-right w-32">Subtotal</th>
                                <th class="px-4 py-3 text-center w-16">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="(item, index) in items" :key="item.unique_id">
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                    <input type="hidden" :name="'products[' + index + '][product_id]'"
                                        :value="item.product_id">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500" x-text="item.code"></td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" x-text="item.name">
                                    </td>
                                    <td class="px-4 py-2"><input type="number" x-model.number="item.quantity"
                                            :name="'products[' + index + '][quantity]'"
                                            class="w-full h-9 rounded-lg border border-gray-300 bg-white text-center dark:bg-gray-900 dark:text-white">
                                    </td>
                                    <td class="px-4 py-2"><input type="number" step="0.01"
                                            x-model.number="item.price" :name="'products[' + index + '][price]'"
                                            class="w-full h-9 rounded-lg border border-gray-300 bg-white text-right dark:bg-gray-900 dark:text-white">
                                    </td>
                                    <td class="px-4 py-2" x-show="!is_global_discount"><input type="number"
                                            step="0.01" x-model.number="item.discount"
                                            :name="'products[' + index + '][discount]'"
                                            class="w-full h-9 rounded-lg border border-gray-300 bg-white text-right dark:bg-gray-900 dark:text-white">
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white"
                                        x-text="'$' + ((item.quantity * item.price) - (is_global_discount ? 0 : item.discount)).toFixed(2)">
                                    </td>
                                    <td class="px-4 py-3 text-center"><button type="button" @click="removeItem(index)"
                                            class="text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 p-1.5 rounded-lg"><svg
                                                class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg></button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>

            <div class="flex flex-col lg:flex-row gap-6 items-start pb-24">
                <div class="w-full lg:w-1/2"></div>
                <x-common.component-card title="Cálculos de Factura" class="w-full lg:w-1/2">
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400"><span>Subtotal
                                Bruto:</span><span class="font-bold text-gray-900 dark:text-white"
                                x-text="'$' + subtotal.toFixed(2)"></span></div>
                        <div class="flex justify-between text-sm text-error-600 font-bold"><span>Descuento
                                Total:</span><span x-text="'-$' + total_discount.toFixed(2)"></span></div>
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400"><span>IVA:</span><span
                                class="font-bold text-gray-900 dark:text-white"
                                x-text="'$' + iva_amount.toFixed(2)"></span></div>
                        <div
                            class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between text-2xl font-black text-brand-600 dark:text-brand-400">
                            <span>TOTAL:</span><span x-text="'$' + total.toFixed(2)"></span></div>
                    </div>
                </x-common.component-card>
            </div>

            <div
                class="fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 p-4 lg:left-72">
                <div class="mx-auto max-w-screen-2xl flex justify-end gap-4">
                    <x-form.button href="{{ route('buys.index') }}" variant="secondary"
                        size="lg">Cancelar</x-form.button>
                    <x-form.button type="submit" variant="primary" size="lg">Registrar Compra</x-form.button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function buyForm(config) {
            return {
                productsData: config.productsData,
                items: [],
                document_type: 'factura',
                is_global_discount: true,
                discount_unit: 'money',
                global_discount_input: 0,
                searchQuery: '',
                searchOpen: false,
                draft: {
                    product: null,
                    quantity: 1,
                    price: 0,
                    discount: 0
                },

                get filteredSearch() {
                    const q = this.searchQuery.toLowerCase().trim();
                    return q === '' ? this.productsData.slice(0, 15) : this.productsData.filter(p => p.name
                    .toLowerCase().includes(q) || p.code.toLowerCase().includes(q)).slice(0, 15);
                },

                selectProduct(p) {
                    this.draft.product = p;
                    this.searchQuery = p.name;
                    this.searchOpen = false;
                },

                addToCart() {
                    if (!this.draft.product || this.draft.quantity < 1) return;
                    const existing = this.items.find(i => i.product_id === this.draft.product.id);
                    const appliedDiscount = this.is_global_discount ? 0 : this.draft.discount;

                    if (existing) {
                        existing.quantity += this.draft.quantity;
                        existing.price = this.draft.price;
                        existing.discount = appliedDiscount;
                    } else {
                        this.items.push({
                            unique_id: Date.now(),
                            product_id: this.draft.product.id,
                            name: this.draft.product.name,
                            code: this.draft.product.code,
                            quantity: this.draft.quantity,
                            price: this.draft.price,
                            discount: appliedDiscount
                        });
                    }
                    this.draft = {
                        product: null,
                        quantity: 1,
                        price: 0,
                        discount: 0
                    };
                    this.searchQuery = '';
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },
                get subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                },
                get total_discount() {
                    if (this.is_global_discount) {
                        return this.discount_unit === 'percent' ? this.subtotal * (this.global_discount_input / 100) :
                            this.global_discount_input;
                    }
                    return this.items.reduce((sum, item) => sum + (item.discount || 0), 0);
                },
                get iva_amount() {
                    const base = this.subtotal - this.total_discount;
                    if (this.document_type === 'credito_fiscal') return base * 0.13;
                    if (this.document_type === 'factura') return base - (base / 1.13);
                    return 0;
                },
                get total() {
                    const base = this.subtotal - this.total_discount;
                    return this.document_type === 'credito_fiscal' ? base + this.iva_amount : base;
                },
                submitForm(e) {
                    if (this.items.length === 0) {
                        e.preventDefault();
                        alert('Debe insertar productos.');
                    }
                }
            }
        }
    </script>
@endsection
