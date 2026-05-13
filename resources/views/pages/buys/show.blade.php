@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detalle de Compra" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        
        {{-- Alerta de Cancelación --}}
        @if($buy->is_cancelled)
            <div class="mb-6 p-4 border border-red-200 bg-red-50 text-red-800 rounded-lg flex items-center gap-3 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <h4 class="font-bold">Transacción Anulada</h4>
                    <p class="text-sm">Esta compra ha sido cancelada. Los productos fueron retirados del inventario.</p>
                </div>
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Documento #{{ $buy->id }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Registrado el {{ \Carbon\Carbon::parse($buy->created_at)->format('d/m/Y \a \l\a\s H:i') }}
                </p>
            </div>
            <div class="flex gap-3">
                @if(!$buy->is_cancelled)
                    <x-form.button href="{{ route('buys.edit', $buy->id) }}" variant="primary" size="md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar Compra
                    </x-form.button>
                @endif
                <x-form.button href="{{ route('buys.index') }}" variant="secondary" size="md">
                    Volver al listado
                </x-form.button>
            </div>
        </div>

        {{-- Tarjeta de información general (Extendida) --}}
        <x-common.component-card title="Información del Documento" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-2">
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Proveedor</span>
                    <span class="text-base font-bold text-gray-900 dark:text-white">
                        {{ $buy->supplier->company_name ?? 'No especificado' }}
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha de Emisión</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ \Carbon\Carbon::parse($buy->date)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo de Documento</span>
                    <span class="text-base font-medium text-brand-600 dark:text-brand-400">
                        @if($buy->document_type === 'factura')
                            Factura (IVA Incluido)
                        @elseif($buy->document_type === 'credito_fiscal')
                            Crédito Fiscal
                        @else
                            Sujeto Exento
                        @endif
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Modalidad de Descuento</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $buy->discount_type === 'global' ? 'Global (Prorrateado)' : 'Por Artículo' }}
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Usuario Registrador</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $buy->user->name ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Oficina Destino</span>
                    <span class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $buy->office->name ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </x-common.component-card>

        {{-- Tabla de productos --}}
        <x-tables.table
            title="Detalle de Productos Ingresados"
            :headers="['Código', 'Producto', 'Cant.', 'P. Unitario', 'Descuento', 'Subtotal Neto']"
            :show-actions="false"
            emptyMessage="No hay productos registrados en esta compra">
            @foreach($buy->details as $detail)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $detail->product->code ?? 'S/C' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $detail->product->name }}</td>
                    
                    {{-- Corrección: Cambiamos text-center y text-right por text-left para alinear con los TH del componente --}}
                    <td class="px-4 py-3 text-left text-gray-700 dark:text-gray-300">{{ $detail->quantity }}</td>
                    <td class="px-4 py-3 text-left text-gray-700 dark:text-gray-300">${{ number_format($detail->price, 2) }}</td>
                    <td class="px-4 py-3 text-left text-error-500">
                        @if($detail->discount > 0)
                            -${{ number_format($detail->discount, 2) }}
                            @if($buy->discount_type === 'global')
                                <span class="block text-[10px] text-gray-400" title="Descuento asignado por prorrateo automático">(Prorrateo)</span>
                            @endif
                        @else
                            $0.00
                        @endif
                    </td>
                    <td class="px-4 py-3 text-left font-bold text-gray-900 dark:text-white">${{ number_format($detail->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </x-tables.table>

        {{-- Tarjeta de resumen financiero (Alineada con la UI de captura) --}}
        <div class="mt-6 flex flex-col lg:flex-row gap-6 items-start pb-20">
            <div class="w-full lg:w-1/2">
                {{-- Espacio para firmas o notas si se requiere en el futuro --}}
            </div>
            <x-common.component-card title="Liquidación Financiera" class="w-full lg:w-1/2">
                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Subtotal Bruto:</span>
                        {{-- Calculamos el bruto sumando el subtotal neto + los descuentos aplicados --}}
                        <span class="font-bold text-gray-900 dark:text-white">${{ number_format($buy->details->sum('subtotal') + $buy->details->sum('discount'), 2) }}</span>
                    </div>
                    
                    @if($buy->discount > 0)
                    <div class="flex justify-between text-sm text-error-600 font-bold">
                        <span>Descuento Total Aplicado:</span>
                        <span>-${{ number_format($buy->discount, 2) }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 border-t border-gray-100 dark:border-gray-800 pt-2">
                        <span>Base Imponible (Neto):</span>
                        <span class="font-medium">${{ number_format($buy->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>
                            @if($buy->document_type === 'credito_fiscal') IVA (+13%):
                            @elseif($buy->document_type === 'factura') IVA Incluido (13%):
                            @else Exento (0%):
                            @endif
                        </span>
                        <span class="font-bold text-gray-900 dark:text-white">${{ number_format($buy->total_iva, 2) }}</span>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between text-2xl font-black text-brand-600 dark:text-brand-400">
                        <span>TOTAL PAGADO:</span>
                        <span>${{ number_format($buy->total, 2) }}</span>
                    </div>
                </div>
            </x-common.component-card>
        </div>

    </div>
@endsection