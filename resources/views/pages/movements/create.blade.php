@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Registrar Ajuste de Inventario" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6">
            <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">Ajuste Manual de Kardex</h2>
            <p class="text-sm text-gray-500">Utilice este módulo para mermas, consumos internos o cuadres de auditoría.</p>
        </div>

        <form action="{{ route('movements.store') }}" method="POST" x-data="movementForm()" @submit="isSubmitting = true">
            @csrf

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Oficina / Sucursal</label>
                        <input type="text" value="{{ auth()->user()->office->name ?? 'No asignada' }}" disabled readonly class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm font-bold text-gray-700 cursor-not-allowed dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                    </div>

                    <x-form.group name="date_movement" label="Fecha del Movimiento" :required="true">
                        <x-form.input type="date" name="date_movement" :value="old('date_movement', date('Y-m-d'))" :required="true" />
                    </x-form.group>

                    <x-form.group name="input_type" label="Comportamiento del Ajuste" :required="true">
                        <select name="input_type" x-model="inputType" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="S">Salida (Merma / Consumo)</option>
                            <option value="E">Entrada (Ajuste por sobrante)</option>
                        </select>
                    </x-form.group>

                    <x-form.group name="type_id" label="Clasificación" :required="true">
                        <select name="type_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                            <option value="">Seleccione el motivo...</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </x-form.group>

                    <div class="md:col-span-2 lg:col-span-4">
                        <x-form.group name="description" label="Justificación / Motivo del ajuste" :required="true">
                            <textarea name="description" rows="2" required class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Ej: Productos caducados, cuadre de fin de mes...">{{ old('description') }}</textarea>
                        </x-form.group>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-4">
                    <h3 class="text-lg font-semibold" :class="inputType === 'S' ? 'text-red-600' : 'text-green-600'" x-text="inputType === 'S' ? 'Productos a Retirar (-)' : 'Productos a Ingresar (+)'"></h3>
                    <x-form.button type="button" variant="secondary" size="sm" @click="addProduct()">
                        Añadir Fila
                    </x-form.button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 w-2/3">Producto</th>
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 text-center w-1/4">Cantidad</th>
                                <th class="px-3 py-3 text-sm font-semibold text-gray-600 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in products" :key="index">
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-3 py-3">
                                        <select :name="'products['+index+'][product_id]'" x-model="item.product_id" required class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm dark:bg-gray-900 dark:text-white/90 dark:border-gray-700">
                                            <option value="">Seleccione un producto...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-3">
                                        <input type="number" step="1" min="1" :name="'products['+index+'][quantity]'" x-model.number="item.quantity" required class="w-full h-11 rounded-lg border border-gray-300 text-center text-sm font-bold dark:bg-gray-900 dark:text-white/90 dark:border-gray-700" />
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <button type="button" @click="removeProduct(index)" class="text-red-500 hover:text-red-700">X</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <x-form.button href="{{ route('movements.index') }}" variant="secondary" size="lg" x-bind:disabled="isSubmitting">Cancelar</x-form.button>
                <x-form.button type="submit" variant="primary" size="lg" x-bind:disabled="isSubmitting" x-bind:class="{'opacity-50 cursor-not-allowed': isSubmitting}">
                    <span x-show="!isSubmitting">Registrar Ajuste</span>
                    <span x-show="isSubmitting" style="display: none;">Procesando...</span>
                </x-form.button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('movementForm', () => ({
                isSubmitting: false,
                inputType: 'S',
                products: [{ product_id: '', quantity: 1 }],
                addProduct() { this.products.push({ product_id: '', quantity: 1 }); },
                removeProduct(index) {
                    if (this.products.length > 1) this.products.splice(index, 1);
                }
            }));
        });
    </script>
@endsection