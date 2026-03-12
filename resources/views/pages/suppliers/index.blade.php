@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Proveedores" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Gestión de Proveedores
                </h2>
            </div>

            <a href="{{ route('suppliers.create') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-colors">
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M10.8333 5V9.16667H15V10.8333H10.8333V15H9.16667V10.8333H5V9.16667H9.16667V5H10.8333Z" />
                </svg>
                Agregar Proveedores
            </a>
        </div>

        {{-- Tabla de Proveedores --}}
        <x-tables.table title="Listado de Proveedores" :headers="['Empresa', 'Nombre Contacto', 'Numero', 'Descripción']" :paginator="$suppliers" :searchable="true"
            emptyMessage="No hay proveedores registrados">
            @foreach ($suppliers as $supplier)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">

                    {{-- Nombre Empresa --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $supplier->company_name }}
                        </span>
                    </td>
                    {{-- Nombre contacto--}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $supplier->contact_name }}
                        </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $supplier->company_name }}
                        </span>
                    </td>

                    {{-- Descripción --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $supplier->description }}
                        </span>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">
                            {{-- Botón Editar --}}
                            <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                class="hover:text-blue-500 transition-colors" title="Editar usuario">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                                    <path
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                        stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </div>
@endsection
