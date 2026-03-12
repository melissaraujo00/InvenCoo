@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Nuevo Producto" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header con título y botones --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Crear Nuevo Producto
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
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            {{-- Tarjeta: Información del Producto --}}
            <x-common.component-card title="" class="mb-1">
                <div class="p-1">
                    {{-- Primera fila: Código y Nombre --}}
                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="code" label="Código" :required="true">
                                <x-form.input
                                    name="code"
                                    placeholder="Ej. PROD-001"
                                    :required="true"
                                    :value="old('code')" />
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="name" label="Nombre" :required="true">
                                <x-form.input
                                    name="name"
                                    placeholder="Ej. Producto 1"
                                    :required="true"
                                    :value="old('name')" />
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
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-form.group>
                        </div>
                    </div>

                    {{-- Tercera fila: Oficina y Stock --}}
                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="office_id" label="Oficina" :required="true">
                                <select name="office_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    <option value="">Seleccione una oficina</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                                            {{ $office->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="stock" label="Stock" :required="true">
                                <x-form.input
                                    type="number"
                                    name="stock"
                                    placeholder="0"
                                    :required="true"
                                    :value="old('stock')" />
                            </x-form.group>
                        </div>
                    </div>

                    {{-- Cuarta fila: Stock Mínimo --}}
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="stock_minimun" label="Stock Mínimo" :required="true">
                                <x-form.input
                                    type="number"
                                    name="stock_minimun"
                                    placeholder="0"
                                    :required="true"
                                    :value="old('stock_minimun')" />
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            {{-- Espacio vacío para mantener simetría --}}
                        </div>
                    </div>
                </div>
            </x-common.component-card>

            {{-- Tarjeta: Proveedores --}}
            <x-common.component-card title="Proveedores" class="mt-6">
                <div class="p-4">
                    <div class="space-y-4" id="suppliers-container">
                        @php $oldSuppliers = old('suppliers', []); @endphp

                        @if(empty($oldSuppliers))
                            {{-- Fila de proveedor inicial --}}
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="w-full md:w-1/2">
                                    <x-form.group name="suppliers[0][id]" label="Proveedor">
                                        <select name="suppliers[0][id]" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                            <option value="">Seleccione un proveedor</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">
                                                    {{ $supplier->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </x-form.group>
                                </div>
                                <div class="w-full md:w-1/2">
                                    <x-form.group name="suppliers[0][price]" label="Precio">
                                        <x-form.input
                                            type="number"
                                            step="0.01"
                                            name="suppliers[0][price]"
                                            placeholder="0.00"
                                            :value="old('suppliers.0.price')" />
                                    </x-form.group>
                                </div>
                            </div>
                        @else
                            @foreach($oldSuppliers as $index => $supplier)
                                <div class="flex flex-col md:flex-row gap-4">
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
                                                :value="old('suppliers.' . $index . '.price')" />
                                        </x-form.group>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Botón para agregar más proveedores --}}
                    <div class="mt-4">
                        <button type="button" id="add-supplier"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-colors">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20">
                                <path d="M10.8333 5V9.16667H15V10.8333H10.8333V15H9.16667V10.8333H5V9.16667H9.16667V5H10.8333Z" />
                            </svg>
                            Agregar otro proveedor
                        </button>
                    </div>
                </div>
            </x-common.component-card>

            {{-- Botones de acción --}}
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
                    Crear Producto
                </x-form.button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('add-supplier').addEventListener('click', function() {
        const container = document.getElementById('suppliers-container');
        const index = container.children.length;

        const div = document.createElement('div');
        div.className = 'flex flex-col md:flex-row gap-4 mt-4';
        div.innerHTML = `
            <div class="w-full md:w-1/2">
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Proveedor</label>
                    <select name="suppliers[${index}][id]" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Seleccione un proveedor</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="w-full md:w-1/2">
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Precio</label>
                    <input type="number" step="0.01" name="suppliers[${index}][price]" placeholder="0.00" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
            </div>
        `;

        container.appendChild(div);
    });
</script>
@endpush
