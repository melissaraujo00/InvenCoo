@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Panel de Auditoría y Control Interno</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Monitoreo de riesgo, trazabilidad y estado global del inventario.</p>
            </div>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-brand-600 dark:hover:bg-brand-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Generar Reportes PDF
            </a>
        </div>

        {{-- SECCIÓN 1: KPIs de Monitoreo --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6 2xl:gap-7.5 mb-6">

            <div class="rounded-2xl border {{ $data['monthly_adjustments'] > 0 ? 'border-red-400 dark:border-red-500 bg-white dark:bg-gray-900' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900' }} p-6 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full {{ $data['monthly_adjustments'] > 0 ? 'bg-red-100 text-red-600 dark:bg-gray-800 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    @if($data['monthly_adjustments'] > 0)
                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800 dark:bg-red-900/30 dark:text-red-400 animate-pulse">Foco de Revisión</span>
                    @endif
                </div>
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white mb-1">{{ $data['monthly_adjustments'] }}</h4>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ajustes Manuales (Este mes)</span>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-gray-800 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white mb-1">{{ $data['current_month_movements'] }}</h4>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Movimientos (Este mes)</span>
                </div>
            </div>

            <div class="rounded-2xl border {{ $data['critical_stock_count'] > 0 ? 'border-orange-400 dark:border-orange-500 bg-white dark:bg-gray-900' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900' }} p-6 shadow-sm transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full {{ $data['critical_stock_count'] > 0 ? 'bg-orange-100 text-orange-600 dark:bg-gray-800 dark:text-orange-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-gray-800 dark:text-white mb-1">{{ $data['critical_stock_count'] }}</h4>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Productos en Stock Crítico</span>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold {{ $data['critical_stock_count'] > 0 ? 'text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300' : 'text-gray-500 hover:text-gray-700' }}">Auditar Catálogo &rarr;</a>
                </div>
            </div>

        </div>

        {{-- SECCIÓN 2: Tabla de Vigilancia (Ajustes Manuales) --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white/90">Registro de Ajustes Manuales Recientes</h3>
                    <p class="text-xs text-gray-500 mt-1">Transacciones que modificaron el inventario sin un flujo de compra o transferencia.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-white dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Transacción</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario Responsable</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Motivo / Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        @forelse($data['recent_adjustments'] as $adjustment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $adjustment->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ $adjustment->transaction_id ?? '#' . $adjustment->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($adjustment->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        {{ $adjustment->user->name ?? 'Sistema' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-md truncate" title="{{ $adjustment->description }}">
                                    {{ $adjustment->description ?? 'Sin justificación registrada' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No se han detectado ajustes manuales recientes. Operación limpia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
