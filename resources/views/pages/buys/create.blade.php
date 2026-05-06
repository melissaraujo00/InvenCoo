@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Nueva Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10" 
         x-data="buyForm({
            oldProducts: {{ Js::from(old('products', [])) }},
            productsData: {{ Js::from($products->map(fn($p) => ['id' => $p->id, 'code' => $p->code, 'name' => $p->name])) }}
         })">
         
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Registrar Compra</h2>
            </div>
            <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="md">
                Volver al listado
            </x-form.button>
        </div>

        <form action="{{ route('buys.store') }}" method="POST" @submit="submitForm($event)">
            @csrf

            {{-- Datos Maestros --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <x-common.component-card title="Encabezado de Factura" class="lg:col-span-2">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative z-30">
                            <x-form.group name="supplier_id" label="Proveedor" :required="true">
                                <x-form.select 
                                    name="supplier_id" 
                                    :options="$suppliers->pluck('company_name', 'id')" 
                                    :value="old('supplier_id')" 
                                    placeholder="Seleccione proveedor" 
                                    searchable />
                            </x-form.group>
                        </div>
                        <x-form.group name="date" label="Fecha de Emisión" :required="true">
                            <x-form.input type="date" name="date" :required="true" :value="old('date', date('Y-m-d'))" />
                        </x-form.group>
                    </div>
                </x-common.component-card>

                <x-common.component-card title="Configuración de Totales">
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <x-form.group name="iva_rate" label="IVA (%)">
                            <x-form.input type="number" step="1" name="iva_rate" x-model.number="iva_rate" placeholder="13" />
                        </x-form.group>
                        <x-form.group name="discount" label="Desc. Global ($)">
                            <x-form.input type="number" step="0.01" name="discount" x-model.number="global_discount" placeholder="0.00" />
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

            {{-- Resumen Financiero --}}
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                <div class="w-full lg:w-1/2"></div>
                <x-common.component-card title="Cálculos de Factura" class="w-full lg:w-1/2">
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900 dark:text-white" x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm text-red-600">
                            <span>Descuento:</span>
                            <span class="font-bold" x-text="'-$' + global_discount.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>IVA (<span x-text="iva_rate"></span>%):</span>
                            <span class="font-bold text-gray-900 dark:text-white" x-text="'$' + iva_amount.toFixed(2)"></span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 flex justify-between text-xl font-black text-brand-600 dark:text-brand-400">
                            <span>TOTAL:</span>
                            <span x-text="'$' + total.toFixed(2)"></span>
                        </div>
                    </div>
                </x-common.component-card>
            </div>

            <div class="flex justify-end gap-4 mt-8">
                <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="lg">Cancelar</x-form.button>
                <x-form.button type="submit" variant="primary" size="lg">Registrar Compra</x-form.button>
            </div>
        </form>
    </div>

    <script>
        function buyForm(config) {
            return {
                productsData: config.productsData,
                items: [],
                iva_rate: 13,
                global_discount: 0,
                searchQuery: '',
                searchOpen: false,
                draft: { product: null, quantity: 1, price: 0 },

                get filteredSearch() {
                    const q = this.searchQuery.toLowerCase().trim();
                    return q === '' ? this.productsData.slice(0, 15) : this.productsData.filter(p => p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q)).slice(0, 15);
                },

                selectProduct(product) {
                    this.draft.product = product;
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
                submitForm(e) { if(this.items.length === 0) { e.preventDefault(); alert('Debe insertar al menos un producto.'); } }
            }
        }
    </script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('products-container');
        const addButton = document.getElementById('add-product-row');
        const discountInput = document.getElementById('discount');
        const ivaRateInput = document.getElementById('iva_rate');

        // Función para recalcular todos los totales
        function recalculateTotals() {
            let subtotal = 0;
            const rows = container.querySelectorAll('.product-row');
            rows.forEach(row => {
                const quantity = parseFloat(row.querySelector('.product-quantity')?.value) || 0;
                const price = parseFloat(row.querySelector('.product-price')?.value) || 0;
                subtotal += quantity * price;
            });

            const discount = parseFloat(discountInput?.value) || 0;
            const subtotalAfterDiscount = subtotal - discount;
            const ivaRate = parseFloat(ivaRateInput?.value) || 0;
            const ivaAmount = subtotalAfterDiscount * (ivaRate / 100);
            const total = subtotalAfterDiscount + ivaAmount;

            // Actualizar displays
            document.getElementById('subtotal_display').innerText = subtotal.toFixed(2);
            document.getElementById('discount_display').innerText = discount.toFixed(2);
            document.getElementById('subtotal_after_discount_display').innerText = subtotalAfterDiscount.toFixed(2);
            document.getElementById('iva_rate_display').innerText = ivaRate;
            document.getElementById('iva_amount_display').innerText = ivaAmount.toFixed(2);
            document.getElementById('total_display').innerText = total.toFixed(2);

            // Actualizar campos ocultos para enviar al servidor
            document.getElementById('subtotal_input').value = subtotal.toFixed(2);
            document.getElementById('total_input').value = total.toFixed(2);
            document.getElementById('total_iva_input').value = ivaAmount.toFixed(2);
        }

        // Función para crear una nueva fila de producto vacía
        function createEmptyRow(index) {
            const div = document.createElement('div');
            div.className = 'product-row flex flex-col md:flex-row gap-4 items-end';
            div.innerHTML = `
                <div class="w-full md:w-1/3">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Producto</label>
                        <select name="products[${index}][product_id]" class="product-select w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm">
                            <option value="">Seleccione un producto</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-full md:w-1/4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Cantidad</label>
                        <input type="number" step="1" name="products[${index}][quantity]" value="1" class="product-quantity w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm">
                    </div>
                </div>
                <div class="w-full md:w-1/4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Precio unitario</label>
                        <input type="number" step="0.01" name="products[${index}][price]" value="0.00" class="product-price w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm">
                    </div>
                </div>
                <div class="flex items-center">
                    <button type="button" class="remove-product-row text-red-500 hover:text-red-700 mb-2">Eliminar</button>
                </div>
            `;
            return div;
        }

        // Agregar fila
        if (addButton) {
            addButton.addEventListener('click', function() {
                const rows = container.querySelectorAll('.product-row');
                const newIndex = rows.length;
                let newRow;
                if (rows.length === 0) {
                    newRow = createEmptyRow(newIndex);
                } else {
                    const firstRow = rows[0];
                    newRow = firstRow.cloneNode(true);
                    // Actualizar índices y limpiar valores
                    newRow.querySelectorAll('select, input').forEach(element => {
                        const name = element.getAttribute('name');
                        if (name) {
                            element.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
                        }
                        if (element.tagName === 'SELECT') element.value = '';
                        else if (element.tagName === 'INPUT') {
                            if (element.classList.contains('product-quantity')) element.value = '1';
                            else if (element.classList.contains('product-price')) element.value = '0.00';
                            else element.value = '';
                        }
                        element.removeAttribute('selected');
                        element.removeAttribute('checked');
                    });
                }
                container.appendChild(newRow);
                attachRowEvents(newRow);
                recalculateTotals();
            });
        }

        // Adjuntar eventos a los inputs de una fila
        function attachRowEvents(row) {
            const quantityInput = row.querySelector('.product-quantity');
            const priceInput = row.querySelector('.product-price');
            if (quantityInput) quantityInput.addEventListener('input', recalculateTotals);
            if (priceInput) priceInput.addEventListener('input', recalculateTotals);
        }

        // Eliminar fila (delegación)
        container.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.remove-product-row');
            if (!removeBtn) return;
            const row = removeBtn.closest('.product-row');
            if (container.querySelectorAll('.product-row').length > 1) {
                row.remove();
                recalculateTotals();
            } else {
                alert('Debe haber al menos un producto en la compra.');
            }
        });

        // Eventos para descuento e IVA
        if (discountInput) discountInput.addEventListener('input', recalculateTotals);
        if (ivaRateInput) ivaRateInput.addEventListener('input', recalculateTotals);

        // Asignar eventos a las filas existentes y recalcular inicial
        document.querySelectorAll('.product-row').forEach(row => attachRowEvents(row));
        recalculateTotals();
    });
</script>
@endpush
