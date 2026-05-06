@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Editar Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10" 
         x-data="buyForm({
            {{-- Mapeo vital: Convertimos los detalles de Eloquent a objetos de JS para Alpine --}}
            dbItems: {{ Js::from($buy->details->map(fn($d) => [
                'unique_id' => $d->id,
                'product_id' => $d->product_id,
                'name' => $d->product->name,
                'code' => $d->product->code,
                'quantity' => (int)$d->quantity,
                'price' => (float)$d->price
            ])) }},
            productsData: {{ Js::from($products->map(fn($p) => ['id' => $p->id, 'code' => $p->code, 'name' => $p->name])) }},
            {{-- Calculamos la tasa de IVA aplicada originalmente --}}
            initialIva: {{ $buy->total_iva > 0 ? round(($buy->total_iva / ($buy->subtotal - $buy->discount)) * 100) : 0 }},
            initialDiscount: {{ (float)$buy->discount }}
         })">
         
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Editar Compra #{{ $buy->id }}</h2>
                <p class="text-sm text-gray-500">Actualice los datos de la factura o el inventario ingresado</p>
            </div>
            <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="md">
                Volver al listado
            </x-form.button>
        </div>

        <form action="{{ route('buys.update', $buy->id) }}" method="POST" @submit="submitForm($event)">
            @csrf
            @method('PUT')

            {{-- Datos de la Factura --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <x-common.component-card title="Encabezado Actualizable" class="lg:col-span-2">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative z-50">
                            <x-form.group name="supplier_id" label="Proveedor" :required="true">
                                <x-form.select 
                                    name="supplier_id" 
                                    :options="$suppliers->pluck('company_name', 'id')" 
                                    :value="old('supplier_id', $buy->supplier_id)" 
                                    searchable />
                            </x-form.group>
                        </div>
                        <x-form.group name="date" label="Fecha de Factura" :required="true">
                            <x-form.input type="date" name="date" :required="true" :value="old('date', $buy->date)" />
                        </x-form.group>
                    </div>
                </x-common.component-card>

                <x-common.component-card title="Ajuste de Impuestos">
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <x-form.group name="iva_rate" label="IVA (%)">
                            <x-form.input type="number" step="1" name="iva_rate" x-model.number="iva_rate" />
                        </x-form.group>
                        <x-form.group name="discount" label="Descuento ($)">
                            <x-form.input type="number" step="0.01" name="discount" x-model.number="global_discount" />
                        </x-form.group>
                    </div>
                </x-common.component-card>
            </div>

            {{-- Captura Estilo POS --}}
            <x-common.component-card title="Detalle de Productos" class="mb-6 overflow-visible">
                
                {{-- 1. Barra de Captura --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-t-xl relative z-40">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        
                        {{-- Buscador Proactivo --}}
                        <div class="md:col-span-5 relative" @click.away="searchOpen = false">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Buscar Producto</label>
                            <input type="text" x-model="searchQuery" @focus="searchOpen = true" @click="searchOpen = true"
                                   placeholder="Haga clic para ver catálogo..." 
                                   class="h-11 w-full rounded-lg border border-brand-300 bg-white px-4 py-2 text-sm focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition-all">
                            
                            <div x-show="searchOpen" class="absolute left-0 right-0 top-full mt-1 max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 z-50" style="display: none;">
                                <template x-for="p in filteredSearch" :key="p.id">
                                    <div @click="selectProduct(p)" class="cursor-pointer px-4 py-3 hover:bg-brand-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <div class="font-medium text-gray-900 dark:text-white" x-text="p.name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono" x-text="p.code"></div>
                                    </div>
                                </template>
                                <div x-show="filteredSearch.length === 0" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    No se encontraron productos.
                                </div>
                            </div>
                        </div>

                        {{-- Cantidad Borrador (Corregido Dark Mode) --}}
                        <div class="md:col-span-2 text-center">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cantidad</label>
                            <input type="number" min="1" x-model.number="draft.quantity" @keyup.enter="addToCart()" 
                                   class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-center focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition-all">
                        </div>

                        {{-- Precio Borrador (Corregido Dark Mode) --}}
                        <div class="md:col-span-3 text-right">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">$ Costo Unit.</label>
                            <input type="number" min="0" step="0.01" x-model.number="draft.price" @keyup.enter="addToCart()" 
                                   class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-right focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition-all">
                        </div>

                        <div class="md:col-span-2">
                            <button type="button" @click="addToCart()" :disabled="!draft.product" 
                                    class="h-11 w-full rounded-lg bg-brand-600 font-medium text-white hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                Insertar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 2. Tabla de Canasta (Corregido Dark Mode) --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-3">Código</th>
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3 text-center w-32">Cantidad</th>
                                <th class="px-4 py-3 text-right w-32">Costo</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                                <th class="px-4 py-3 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="item.unique_id">
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                    <input type="hidden" :name="'products['+index+'][product_id]'" :value="item.product_id">
                                    
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400" x-text="item.code"></td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" x-text="item.name"></td>
                                    
                                    {{-- Input Cantidad en Tabla (Corregido Dark Mode) --}}
                                    <td class="px-4 py-2">
                                        <input type="number" x-model.number="item.quantity" :name="'products['+index+'][quantity]'" 
                                               class="w-full max-w-[5rem] mx-auto block h-9 rounded-lg border border-gray-300 bg-white px-2 text-center text-sm focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition-all">
                                    </td>
                                    
                                    {{-- Input Precio en Tabla (Corregido Dark Mode) --}}
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" x-model.number="item.price" :name="'products['+index+'][price]'" 
                                               class="w-full max-w-[6rem] ml-auto block h-9 rounded-lg border border-gray-300 bg-white px-2 text-right text-sm focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition-all">
                                    </td>
                                    
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white" x-text="'$' + (item.quantity * item.price).toFixed(2)"></td>
                                    
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="removeItem(index)" class="text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 p-1.5 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            
                            <tr x-show="items.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    La canasta está vacía. Busque un producto para comenzar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>

            {{-- Totales --}}
            <div class="flex justify-end mb-10">
                <x-common.component-card title="Totales Re-calculados" class="w-full lg:w-1/2">
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900 dark:text-white" x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm text-red-600 font-bold">
                            <span>Descuento:</span>
                            <span x-text="'-$' + global_discount.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>IVA (<span x-text="iva_rate"></span>%):</span>
                            <span class="font-bold text-gray-900 dark:text-white" x-text="'$' + iva_amount.toFixed(2)"></span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 flex justify-between text-xl font-black text-brand-600 dark:text-brand-400">
                            <span>NUEVO TOTAL:</span>
                            <span x-text="'$' + total.toFixed(2)"></span>
                        </div>
                    </div>
                </x-common.component-card>
            </div>

            <div class="flex justify-end gap-4">
                <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="lg">Cancelar</x-form.button>
                <x-form.button type="submit" variant="primary" size="lg">Actualizar Compra</x-form.button>
            </div>
        </form>
    </div>

    <script>
        function buyForm(config) {
            return {
                productsData: config.productsData,
                {{-- Aquí está la clave: inicializamos con los datos de la DB mapeados --}}
                items: config.dbItems,
                iva_rate: config.initialIva,
                global_discount: config.initialDiscount,
                searchQuery: '',
                searchOpen: false,
                draft: { product: null, quantity: 1, price: 0 },

                get filteredSearch() {
                    const q = this.searchQuery.toLowerCase().trim();
                    return q === '' ? this.productsData.slice(0, 15) : this.productsData.filter(p => p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q)).slice(0, 15);
                },

                selectProduct(product) {
                    this.draft.product = product;
                    this.draft.price = 0; {{-- Opcional: podrías buscar el último precio de este producto --}}
                    this.searchQuery = product.name;
                    this.searchOpen = false;
                },

                addToCart() {
                    if (!this.draft.product || this.draft.quantity < 1) return;
                    const existing = this.items.find(i => i.product_id === this.draft.product.id);
                    if (existing) {
                        existing.quantity += this.draft.quantity;
                        existing.price = this.draft.price;
                    } else {
                        this.items.push({
                            unique_id: Date.now(),
                            product_id: this.draft.product.id,
                            name: this.draft.product.name,
                            code: this.draft.product.code,
                            quantity: this.draft.quantity,
                            price: this.draft.price
                        });
                    }
                    this.draft = { product: null, quantity: 1, price: 0 };
                    this.searchQuery = '';
                },

                removeItem(index) { this.items.splice(index, 1); },
                get subtotal() { return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0); },
                get iva_amount() { return Math.max(0, (this.subtotal - this.global_discount)) * (this.iva_rate / 100); },
                get total() { return Math.max(0, (this.subtotal - this.global_discount)) + this.iva_amount; },
                submitForm(e) { if(this.items.length === 0) { e.preventDefault(); alert('Debe haber al menos un producto.'); } }
            }
        }
    </script>
@endsection