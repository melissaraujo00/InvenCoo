@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Productos" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Gestión de Productos
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">
                    Inventario central y catálogo general
                </p>
            </div>

            <x-form.button href="{{ route('products.create') }}" variant="primary" size="md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Producto
            </x-form.button>
        </div>

        {{-- Tabla de Productos (Omitimos 'Acciones' en el array por el componente base) --}}
        <x-tables.table
            title="Listado de Productos"
            :headers="['Código', 'Nombre', 'Categoría', 'Marca', 'Stock', 'Unidad', 'Oficina', 'Proveedores']"
            :paginator="$products"
            :searchable="true"
            emptyMessage="No hay productos registrados">

            @foreach ($products as $product)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    {{-- Código --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700">
                            {{ $product->code }}
                        </span>
                    </td>

                    {{-- Nombre --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $product->name }}
                        </span>
                    </td>

                    {{-- Categoría --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $product->category->name ?? 'N/A' }}
                        </span>
                    </td>

                    {{-- Marca --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $product->brand->name ?? 'N/A' }}
                        </span>
                    </td>

                    {{-- Stock --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($product->stock <= $product->stock_minimun)
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/20 dark:text-red-400 font-bold">
                                {{ $product->stock }}
                            </span>
                        @else
                            <span class="text-sm text-gray-700 dark:text-gray-300 font-bold">
                                {{ $product->stock }}
                            </span>
                        @endif
                    </td>

                    {{-- Unidad --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $product->unit }}
                        </span>
                    </td>

                    {{-- Oficina --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $product->office->name ?? 'N/A' }}
                        </span>
                    </td>

                    {{-- Proveedores --}}
                    <td class="px-4 py-4">
                        <div class="flex flex-wrap gap-1 max-w-[200px]">
                            @forelse($product->suppliers as $supplier)
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-900/50"
                                      title="Precio: ${{ number_format($supplier->pivot->price, 2) }}">
                                    {{ $supplier->company_name }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 dark:text-gray-600 italic">
                                    Sin proveedores
                                </span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">

                            {{-- Botón Editar --}}
                            <a href="{{ route('products.edit', $product->id) }}"
                                class="hover:text-yellow-600 transition-colors p-1" title="Editar producto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>

                            {{-- Modal de Eliminar --}}
                            <x-modal.confirmation 
                                title="Eliminar Producto" 
                                :message="'¿Estás seguro que deseas eliminar el producto'" 
                                :itemName="$product->code . ' - ' . $product->name"
                                warning="Se eliminará del inventario y no podrá ser facturado." 
                                confirmText="Sí, eliminar"
                                confirmVariant="danger" 
                                :action="route('products.destroy', $product->id)" 
                                method="DELETE" 
                                icon="danger"
                            >
                                <x-slot name="trigger">
                                    <button class="hover:text-red-500 transition-colors p-1" title="Eliminar producto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </x-slot>
                            </x-modal.confirmation>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection