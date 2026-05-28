@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Centro de Reportes" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Generador de Documentos</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Selecciona el módulo y aplica los filtros necesarios para exportar tu información.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-gray-800 dark:text-brand-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Auditoría de Movimientos</h3>
                        <p class="text-xs text-gray-500">Historial de transferencias entre sucursales.</p>
                    </div>
                </div>

                <form action="{{ route('reports.movements') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Desde</label>
                            <input type="date" name="start_date" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Hasta</label>
                            <input type="date" name="end_date" required class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-gray-700 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Tipo de Movimiento</label>
                        <select name="type_id" class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-gray-700 dark:text-white dark:bg-gray-900">
                            <option value="">Todos los movimientos</option>
                            <option value="1">Compras</option>
                            <option value="2">Salida</option>
                            <option value="3">Devoluciones</option>
                            <option value="4">Ajustes</option>
                            <option value="5">Transferencia salida</option>
                            <option value="6">Transferencia Entrada</option>
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1">*Nota: Los IDs de tipo deben coincidir con tu base de datos.</p>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-gray-900 py-2.5 text-sm font-medium text-white hover:bg-gray-800 dark:bg-brand-600 dark:hover:bg-brand-500 transition-colors">
                        Descargar PDF
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-500 dark:bg-gray-800 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Catálogo y Stock Global</h3>
                        <p class="text-xs text-gray-500">Listado de productos y existencias actuales.</p>
                    </div>
                </div>

                <form action="{{ route('reports.products') }}" method="GET" class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Este reporte no requiere filtros de fecha, ya que evalúa el inventario en tiempo real.</p>

                    <div class="pt-5">
                        <button type="submit" class="w-full rounded-lg bg-gray-900 py-2.5 text-sm font-medium text-white hover:bg-gray-800 dark:bg-brand-600 dark:hover:bg-brand-500 transition-colors">
                            Descargar PDF
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
