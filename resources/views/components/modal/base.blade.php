@props([
    'id' => null,
    'maxWidth' => 'lg', // sm, md, lg, xl, 2xl, full
    'show' => false,
    'title' => null,
    'closeable' => true,
])

@php
$maxWidthClasses = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    'full' => 'sm:max-w-full sm:w-full mx-4',
][$maxWidth];

$id = $id ?? 'modal-' . uniqid();
@endphp

<div x-data="{
    show: @js($show),
    closeable: @js($closeable)
}"
     x-init="$watch('show', value => {
         if (value) {
             document.body.style.overflow = 'hidden';
         } else {
             document.body.style.overflow = '';
         }
     })"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    {{-- Overlay --}}
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"
         @click="if (closeable) show = false">
    </div>

    {{-- Modal Content --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full {{ $maxWidthClasses }} mx-auto"
             @click.away="if (closeable) show = false">

            {{-- Header con título y botón cerrar --}}
            @if($title || $closeable)
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                @if($title)
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $title }}
                    </h3>
                @endif

                @if($closeable)
                    <button @click="show = false"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
            @endif

            {{-- Body --}}
            <div class="px-6 py-4">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @isset($footer)
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
