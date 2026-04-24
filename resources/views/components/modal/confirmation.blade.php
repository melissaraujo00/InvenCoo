@props([
    'trigger' => null, // Slot para el botón que abre el modal
    'title' => 'Confirmar Acción',
    'message' => '¿Estás seguro que deseas realizar esta acción?',
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
    'confirmVariant' => 'danger', // primary, danger, success, warning
    'action' => null, // Ruta o URL para la acción
    'method' => 'POST', // Método HTTP
    'itemName' => null, // Nombre del item para mostrar (opcional)
    'warning' => 'Esta acción no se puede deshacer.',
    'icon' => 'warning', // warning, danger, info, success
    'size' => 'lg', // sm, md, lg, xl
])

@php
$iconConfig = [
    'warning' => [
        'bg' => 'bg-yellow-100 dark:bg-yellow-500/10',
        'text' => 'text-yellow-600 dark:text-yellow-500',
        'path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    ],
    'danger' => [
        'bg' => 'bg-red-100 dark:bg-red-500/10',
        'text' => 'text-red-600 dark:text-red-500',
        'path' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    'info' => [
        'bg' => 'bg-blue-100 dark:bg-blue-500/10',
        'text' => 'text-blue-600 dark:text-blue-500',
        'path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
    'success' => [
        'bg' => 'bg-green-100 dark:bg-green-500/10',
        'text' => 'text-green-600 dark:text-green-500',
        'path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    ],
];

$buttonVariants = [
    'primary' => 'bg-brand-600 hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-600',
    'danger' => 'bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600',
    'success' => 'bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600',
];

$sizeClasses = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
];

$iconData = $iconConfig[$icon] ?? $iconConfig['warning'];
@endphp

<div x-data="{ open: false }" class="inline">
    {{-- Trigger Button --}}
    <div @click="open = true" class="inline">
        {{ $trigger }}
    </div>

    {{-- Modal --}}
    <div x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto whitespace-normal text-left"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"
             @click="open = false">
        </div>

        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full {{ $sizeClasses[$size] }} mx-auto"
                 @click.away="open = false">

                {{-- Body --}}
                <div class="px-6 py-4">
                    <div class="sm:flex sm:items-start">
                        {{-- Icono --}}
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full {{ $iconData['bg'] }} sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 {{ $iconData['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconData['path'] }}" />
                            </svg>
                        </div>

                        {{-- Texto --}}
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1 min-w-0">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">
                                {{ $title }}
                            </h3>

                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400 break-words">
                                    {{ $message }}
                                    @if($itemName)
                                        <span class="font-semibold text-gray-700 dark:text-gray-300 break-words block sm:inline mt-1 sm:mt-0">
                                            "{{ $itemName }}"
                                        </span>
                                    @endif
                                </p>

                                @if($warning)
                                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-500/10 rounded-lg border border-red-200 dark:border-red-800">
                                        <p class="text-sm text-red-600 dark:text-red-400 break-words">
                                            <span class="font-semibold">⚠️ Atención:</span> {{ $warning }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                        {{-- Botón Cancelar --}}
                        <button type="button"
                                @click="open = false"
                                class="inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-700 sm:w-auto transition-colors">
                            {{ $cancelText }}
                        </button>

                        {{-- Botón Confirmar --}}
                        @if($action)
                            <form action="{{ $action }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                @method($method)
                                <button type="submit"
                                        class="inline-flex w-full justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm {{ $buttonVariants[$confirmVariant] }} sm:w-auto transition-colors">
                                    {{ $confirmText }}
                                </button>
                            </form>
                        @else
                            <button type="button"
                                    @click="$dispatch('confirm'); open = false"
                                    class="inline-flex w-full justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm {{ $buttonVariants[$confirmVariant] }} sm:w-auto transition-colors">
                                {{ $confirmText }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
