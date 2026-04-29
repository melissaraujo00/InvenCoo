@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Nuevo Producto" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Crear Nuevo Producto
                </h2>
            </div>

            <div class="flex gap-3">
                <x-form.button href="{{ route('products.index') }}" variant="secondary" size="md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al listado
                </x-form.button>
            </div>
        </div>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <x-common.component-card title="Información Principal" class="mb-6">
                <div class="p-6">
                    
                    {{-- PRIMERA FILA: Código Simétrico y Nombre --}}
                    <div class="flex flex-col md:flex-row gap-6 mb-6">
                        <div class="w-full md:w-1/2">
                            <x-form.group name="code" label="Código (Autogenerado)" :required="false">
                                <div class="h-11 flex items-center rounded-lg border border-gray-200 bg-gray-50 px-4 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800/50 dark:text-gray-500 font-mono italic select-none">
                                    [Generado al guardar]
                                </div>
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2">
                            <x-form.group name="name" label="Nombre del Producto" :required="true">
                                <x-form.input name="name" placeholder="Ej. Harina Fuerte" :required="true" :value="old('name')" />
                            </x-form.group>
                        </div>
                    </div>

                    {{-- SEGUNDA FILA: Categoría y Marca --}}
                    <div class="flex flex-col md:flex-row gap-6 mb-6">
                        <div class="w-full md:w-1/2 relative z-30">
                            <x-form.group name="category_id" label="Categoría" :required="true">
                                <x-form.select 
                                    name="category_id" 
                                    :options="$categories->pluck('name', 'id')" 
                                    :value="old('category_id')" 
                                    placeholder="Seleccione una categoría" 
                                    searchable />
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2 relative z-20">
                            <x-form.group name="brand_id" label="Marca" :required="false">
                                <x-form.select 
                                    name="brand_id" 
                                    :options="$brands->pluck('name', 'id')" 
                                    :value="old('brand_id')" 
                                    placeholder="Seleccione una marca (opcional)" 
                                    searchable />
                            </x-form.group>
                        </div>
                    </div>

                    {{-- TERCERA FILA: Stock, Mínimo y Unidad --}}
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="w-full md:w-1/3">
                            <x-form.group name="stock" label="Stock Inicial" :required="true">
                                <x-form.input type="number" name="stock" placeholder="0" :required="true" :value="old('stock')" />
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/3">
                            <x-form.group name="stock_minimun" label="Stock Mínimo" :required="true">
                                <x-form.input type="number" name="stock_minimun" placeholder="0" :required="true" :value="old('stock_minimun')" />
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/3">
                            <x-form.group name="unit" label="Unidad de Medida" :required="true">
                                <x-form.input type="text" name="unit" placeholder="Caja, Litro, Unidad" :required="true" :value="old('unit')" />
                            </x-form.group>
                        </div>
                    </div>
                </div>
            </x-common.component-card>

            {{-- Tarjeta 2: Proveedores (Motor Reactivo con Validaciones y Select Premium) --}}
            <x-common.component-card title="Proveedores y Costos" class="mb-6">
                <div class="p-6" 
                     x-data="{ 
                        // 1. Cargamos datos viejos, datos de DB, o array vacío
                        suppliers: {{ Js::from(old('suppliers', isset($product) && $product->suppliers->count() > 0 ? $product->suppliers->map(fn($s) => ['id' => $s->id, 'price' => $s->pivot->price])->toArray() : [['id' => '', 'price' => '']])) }},
                        
                        // 2. Inyectamos los errores de Laravel y las opciones de proveedores a JS
                        serverErrors: {{ Js::from($errors->toArray()) }},
                        supplierOptions: {{ Js::from($suppliers->pluck('company_name', 'id')) }},
                        
                        init() { if(this.suppliers.length === 0) this.addSupplier() },
                        addSupplier() { this.suppliers.push({id: '', price: ''}) },
                        removeSupplier(index) { if(this.suppliers.length > 1) this.suppliers.splice(index, 1) },
                        
                        // 3. Funciones para detectar errores específicos de cada fila
                        hasError(field, index) {
                            return this.serverErrors['suppliers.' + index + '.' + field] !== undefined;
                        },
                        getError(field, index) {
                            return this.serverErrors['suppliers.' + index + '.' + field][0];
                        }
                     }">
                     
                    <div class="space-y-4 relative z-10">
                        <template x-for="(supplier, index) in suppliers" :key="index">
                            <div class="flex flex-col md:flex-row gap-4 items-start bg-gray-50/50 dark:bg-gray-800/20 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                                
                                {{-- PROVEEDOR: Select Premium con Buscador Integrado --}}
                                <div class="w-full md:w-1/2">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Proveedor</label>
                                    
                                    <div x-data="{
                                            open: false,
                                            search: '',
                                            get filtered() {
                                                if (this.search === '') return supplierOptions;
                                                const lower = this.search.toLowerCase();
                                                return Object.fromEntries(
                                                    Object.entries(supplierOptions).filter(([val, label]) => label.toLowerCase().includes(lower))
                                                );
                                            }
                                        }" class="relative z-20">
                                        
                                        {{-- Trigger del Select --}}
                                        <div @click="open = !open" 
                                             @click.away="open = false"
                                             :class="hasError('id', index) ? 'border-error-300 focus:ring-error-500/10' : 'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10'"
                                             class="relative flex h-11 w-full cursor-pointer items-center justify-between rounded-lg border bg-white px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                            <span x-text="supplier.id ? supplierOptions[supplier.id] : 'Seleccione un proveedor'" class="truncate" :class="!supplier.id ? 'text-gray-400' : ''"></span>
                                            <svg class="stroke-current text-gray-500" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </div>

                                        {{-- Dropdown con Buscador --}}
                                        <div x-show="open" style="display: none;" class="absolute left-0 right-0 z-50 mt-1 max-h-60 overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900">
                                            <div class="sticky top-0 bg-white p-2 dark:bg-gray-900">
                                                <input type="text" x-model="search" placeholder="Buscar proveedor..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                                            </div>
                                            <div class="p-1">
                                                <template x-for="(label, value) in filtered" :key="value">
                                                    <div @click="supplier.id = value; open = false; search = ''"
                                                         :class="supplier.id == value ? 'bg-brand-50 dark:bg-brand-800/30 font-medium' : ''"
                                                         class="cursor-pointer rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-brand-50 dark:text-gray-300 dark:hover:bg-brand-800/30"
                                                         x-text="label"></div>
                                                </template>
                                                <div x-show="Object.keys(filtered).length === 0" class="px-3 py-2 text-sm text-gray-500 text-center">Sin resultados</div>
                                            </div>
                                        </div>

                                        {{-- Input Oculto para Laravel y Mensaje de Error --}}
                                        <input type="hidden" x-model="supplier.id" x-bind:name="'suppliers[' + index + '][id]'">
                                        <p x-show="hasError('id', index)" x-text="hasError('id', index) ? getError('id', index) : ''" class="mt-1.5 text-xs text-error-500"></p>
                                    </div>
                                </div>
                                
                                {{-- PRECIO DE COSTO --}}
                                <div class="w-full md:w-2/5">
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Precio pactado</label>
                                    <input type="number" step="0.01" x-model="supplier.price" x-bind:name="'suppliers[' + index + '][price]'" placeholder="0.00" 
                                           :class="hasError('price', index) ? 'border-error-300 focus:border-error-300 focus:ring-error-500/10' : 'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10'"
                                           class="h-11 w-full rounded-lg border bg-white px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 outline-none">
                                    <p x-show="hasError('price', index)" x-text="hasError('price', index) ? getError('price', index) : ''" class="mt-1.5 text-xs text-error-500"></p>
                                </div>

                                {{-- BOTÓN ELIMINAR FILA --}}
                                <div class="w-full md:w-auto flex items-end h-[68px]">
                                    <button type="button" @click="removeSupplier(index)" x-show="suppliers.length > 1" class="text-error-500 hover:text-error-700 bg-error-50 hover:bg-error-100 p-2.5 rounded-lg transition-colors dark:bg-error-500/10 dark:hover:bg-error-500/20">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- BOTÓN AGREGAR PROVEEDOR --}}
                    <div class="mt-4">
                        <button type="button" @click="addSupplier()" class="inline-flex items-center gap-2 rounded-lg border border-brand-200 bg-brand-50 px-4 py-2.5 text-sm font-medium text-brand-700 hover:bg-brand-100 dark:border-brand-800 dark:bg-brand-900/20 dark:text-brand-400 dark:hover:bg-brand-900/40 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Asociar otro proveedor
                        </button>
                    </div>
                </div>
            </x-common.component-card>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
                <x-form.button type="button" href="{{ route('products.index') }}" variant="secondary" size="lg">
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