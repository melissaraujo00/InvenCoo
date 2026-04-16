@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Nueva Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header con título y botones --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Registrar Compra
                </h2>
            </div>

            <div class="flex gap-3">
                <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al listado
                </x-form.button>
            </div>
        </div>

        {{-- Formulario principal --}}
        <form action="{{ route('buys.store') }}" method="POST">
            @csrf

            {{-- Tarjeta: Información de la Compra --}}
            <x-common.component-card title="" class="mb-1">
                <div class="p-1">
                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.select
                                name="supplier_id"
                                label="Proveedor"
                                :options="$suppliers->pluck('company_name', 'id')->toArray()"
                                :value="old('supplier_id')"
                                placeholder="Seleccione un proveedor (opcional)"
                            />
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="date" label="Fecha" :required="true">
                                <x-form.input
                                    type="date"
                                    name="date"
                                    :required="true"
                                    :value="old('date', date('Y-m-d'))"
                                />
                            </x-form.group>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="discount" label="Descuento (monto fijo)">
                                <x-form.input
                                    type="number"
                                    step="0.01"
                                    name="discount"
                                    id="discount"
                                    placeholder="0.00"
                                    :value="old('discount', 0)"
                                />
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="iva_rate" label="IVA (%)">
                                <x-form.input
                                    type="number"
                                    step="0.01"
                                    name="iva_rate"
                                    id="iva_rate"
                                    placeholder="0"
                                    :value="old('iva_rate', 0)"
                                />
                            </x-form.group>
                        </div>
                    </div>
                </div>
            </x-common.component-card>

            {{-- Tarjeta: Productos --}}
            <x-common.component-card title="Productos" class="mt-6">
                <div class="p-4">
                    <div class="space-y-4" id="products-container">
                        @php
                            $productsList = old('products', [['product_id' => '', 'quantity' => 1, 'price' => 0]]);
                        @endphp

                        @foreach($productsList as $index => $item)
                            <div class="product-row flex flex-col md:flex-row gap-4 items-end">
                                <div class="w-full md:w-1/3">
                                    <x-form.group name="products[{{ $index }}][product_id]" label="Producto" :required="true">
                                        <select name="products[{{ $index }}][product_id]" class="product-select w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm">
                                            <option value="">Seleccione un producto</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ ($item['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </x-form.group>
                                </div>
                                <div class="w-full md:w-1/4">
                                    <x-form.group name="products[{{ $index }}][quantity]" label="Cantidad" :required="true">
                                        <x-form.input
                                            type="number"
                                            step="1"
                                            class="product-quantity"
                                            name="products[{{ $index }}][quantity]"
                                            placeholder="1"
                                            :required="true"
                                            :value="old('products.' . $index . '.quantity', $item['quantity'] ?? 1)"
                                        />
                                    </x-form.group>
                                </div>
                                <div class="w-full md:w-1/4">
                                    <x-form.group name="products[{{ $index }}][price]" label="Precio unitario" :required="true">
                                        <x-form.input
                                            type="number"
                                            step="0.01"
                                            class="product-price"
                                            name="products[{{ $index }}][price]"
                                            placeholder="0.00"
                                            :required="true"
                                            :value="old('products.' . $index . '.price', $item['price'] ?? 0)"
                                        />
                                    </x-form.group>
                                </div>
                                <div class="flex items-center">
                                    <button type="button" class="remove-product-row text-red-500 hover:text-red-700 mb-2">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-product-row"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-colors">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                            <path d="M10.8333 5V9.16667H15V10.8333H10.8333V15H9.16667V10.8333H5V9.16667H9.16667V5H10.8333Z" />
                        </svg>
                        Agregar otro producto
                    </button>
                </div>
            </x-common.component-card>

            {{-- Tarjeta de Totales --}}
            <x-common.component-card title="Resumen de Totales" class="mt-6">
                <div class="p-4 space-y-2">
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Subtotal:</span>
                        <span id="subtotal_display">0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Descuento:</span>
                        <span id="discount_display">0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>Subtotal después de descuento:</span>
                        <span id="subtotal_after_discount_display">0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>IVA (<span id="iva_rate_display">0</span>%):</span>
                        <span id="iva_amount_display">0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                        <span>Total:</span>
                        <span id="total_display">0.00</span>
                    </div>
                </div>
            </x-common.component-card>

            {{-- Botones de acción --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
                <x-form.button type="button" href="{{ route('buys.index') }}" variant="secondary" size="lg">
                    Cancelar
                </x-form.button>
                <x-form.button type="submit" variant="primary" size="lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Registrar Compra
                </x-form.button>
            </div>

            {{-- Campos ocultos para enviar los totales calculados (opcional) --}}
            <input type="hidden" name="subtotal" id="subtotal_input" value="0">
            <input type="hidden" name="total" id="total_input" value="0">
            <input type="hidden" name="total_iva" id="total_iva_input" value="0">
        </form>
    </div>
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
