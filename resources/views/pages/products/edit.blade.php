@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Editar Producto" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header con título y botones --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Editar Producto: {{ $product->name }}
                </h2>
            </div>

            <div class="flex gap-3">
                <x-form.button href="{{ route('products.index') }}" variant="secondary" size="md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al listado
                </x-form.button>
            </div>
        </div>

        {{-- Formulario principal --}}
        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Tarjeta: Información del Producto --}}
            <x-common.component-card title="" class="mb-1">
                <div class="p-1">
                    {{-- Primera fila: Código (solo lectura) y Nombre --}}
                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="code" label="Código" :required="false">
                                {{-- Mostrar el código como texto plano (no editable) --}}
                                <div class="rounded-lg border border-gray-300 bg-gray-100 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->code }}
                                </div>
                                {{-- Campo oculto para enviar el código (opcional, si se requiere en el request) --}}
                                <input type="hidden" name="code" value="{{ $product->code }}">
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="name" label="Nombre" :required="true">
                                <x-form.input
                                    name="name"
                                    placeholder="Ej. Producto 1"
                                    :required="true"
                                    :value="old('name', $product->name)" />
                            </x-form.group>
                        </div>
                    </div>

                    {{-- Segunda fila: Categoría y Marca --}}
                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="category_id" label="Categoría" :required="true">
                                <select name="category_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="brand_id" label="Marca">
                                <select name="brand_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    <option value="">Seleccione una marca</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-form.group>
                        </div>
                    </div>

                    {{-- Tercera fila: Stock y Stock Mínimo --}}
                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="stock" label="Stock" :required="true">
                                <x-form.input
                                    type="number"
                                    name="stock"
                                    placeholder="0"
                                    :required="true"
                                    :value="old('stock', $product->stock)" />
                            </x-form.group>
                        </div>

                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="stock_minimun" label="Stock Mínimo" :required="true">
                                <x-form.input
                                    type="number"
                                    name="stock_minimun"
                                    placeholder="0"
                                    :required="true"
                                    :value="old('stock_minimun', $product->stock_minimun)" />
                            </x-form.group>
                        </div>
                    </div>

                    {{-- Cuarta fila: Unidad --}}
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="unit" label="Unidad" :required="true">
                                <x-form.input
                                    type="text"
                                    name="unit"
                                    placeholder="Caja"
                                    :required="true"
                                    :value="old('unit', $product->unit)" />
                            </x-form.group>
                        </div>
                    </div>
                </div>
            </x-common.component-card>

            {{-- Tarjeta: Proveedores --}}
            <x-common.component-card title="Proveedores" class="mt-6">
                <div class="p-4">
                    <div class="space-y-4" id="suppliers-container">
                        @php
                            // Preparar la lista de proveedores: si hay old (error de validación) se usa, si no, los existentes
                            $suppliersList = old('suppliers', $product->suppliers->map(function($supplier) {
                                return [
                                    'id' => $supplier->id,
                                    'price' => $supplier->pivot->price
                                ];
                            })->toArray());

                            if (empty($suppliersList)) {
                                $suppliersList = [['id' => '', 'price' => '']];
                            }
                        @endphp

                        @foreach($suppliersList as $index => $supplier)
                            <div class="supplier-row flex flex-col md:flex-row gap-4">
                                <div class="w-full md:w-1/2">
                                    <x-form.group name="suppliers[{{ $index }}][id]" label="Proveedor">
                                        <select name="suppliers[{{ $index }}][id]" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                            <option value="">Seleccione un proveedor</option>
                                            @foreach($suppliers as $supplierItem)
                                                <option value="{{ $supplierItem->id }}" {{ $supplier['id'] == $supplierItem->id ? 'selected' : '' }}>
                                                    {{ $supplierItem->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </x-form.group>
                                </div>
                                <div class="w-full md:w-1/2">
                                    <x-form.group name="suppliers[{{ $index }}][price]" label="Precio">
                                        <x-form.input
                                            type="number"
                                            step="0.01"
                                            name="suppliers[{{ $index }}][price]"
                                            placeholder="0.00"
                                            :value="old('suppliers.' . $index . '.price', $supplier['price'])" />
                                    </x-form.group>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="button" id="add-supplier-row"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-colors">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                                <path d="M10.8333 5V9.16667H15V10.8333H10.8333V15H9.16667V10.8333H5V9.16667H9.16667V5H10.8333Z" />
                            </svg>
                            Agregar otro proveedor
                        </button>
                    </div>
                </div>
            </x-common.component-card>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
                <x-form.button
                    type="button"
                    href="{{ route('products.index') }}"
                    variant="secondary"
                    size="lg">
                    Cancelar
                </x-form.button>

                <x-form.button type="submit" variant="primary" size="lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Actualizar Producto
                </x-form.button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('suppliers-container');
        const addButton = document.getElementById('add-supplier-row');

        if (!container || !addButton) return;

        addButton.addEventListener('click', function() {
            const rows = container.querySelectorAll('.supplier-row');
            const newIndex = rows.length;

            const firstRow = rows[0];
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('select, input').forEach(function(element) {
                const name = element.getAttribute('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, '[' + newIndex + ']');
                    element.setAttribute('name', newName);
                }
                if (element.tagName === 'SELECT') element.value = '';
                else if (element.tagName === 'INPUT') element.value = '';
                element.removeAttribute('selected');
                element.removeAttribute('checked');
            });

            container.appendChild(newRow);
        });
    });
</script>
@endpush
