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
            </div>

            <a href="{{ route('products.create') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-colors">
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M10.8333 5V9.16667H15V10.8333H10.8333V15H9.16667V10.8333H5V9.16667H9.16667V5H10.8333Z" />
                </svg>
                Agregar Producto
            </a>
        </div>

        {{-- Tabla de Productos --}}
        <x-tables.table
            title="Listado de Productos"
            :headers="['Código', 'Nombre', 'Categoría', 'Marca', 'Stock', 'Unidad','Oficina', 'Proveedores']"
            :paginator="$products"
            :searchable="true"
            emptyMessage="No hay productos registrados">

            @foreach ($products as $product)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    {{-- Código --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-800 dark:text-white/90">
                            {{ $product->code }}
                        </span>
                    </td>

                    {{-- Nombre --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
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
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                {{ $product->stock }}
                            </span>
                        @else
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $product->stock }}
                            </span>
                        @endif
                    </td>

                    {{-- Unidad Mínimo --}}
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
                        <div class="flex flex-wrap gap-1">
                            @forelse($product->suppliers as $supplier)
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                                      title="Precio: ${{ number_format($supplier->pivot->price, 2) }}">
                                    {{ $supplier->company_name }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 dark:text-gray-600">
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
                                class="hover:text-green-500 transition-colors" title="Editar producto">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                    <path
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                        stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>

                            {{-- Botón Eliminar --}}
                            <form action="{{ route('products.destroy', $product->id) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="hover:text-red-500 transition-colors" title="Eliminar producto">
                                    <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection

