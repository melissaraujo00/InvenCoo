@props([
    'route',
    'id',
    'itemName' => null,
])

<div x-data="{ open: false }" class="inline">
    {{-- Botón que abre el modal --}}
    <button @click="open = true"
            {{ $attributes->merge(['class' => 'hover:text-red-500 transition-colors']) }}
            title="Eliminar">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    {{-- Modal --}}
    <div x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0,0,0,0.5);">

        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6" @click.away="open = false">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Eliminar Usuario
            </h3>

            <p class="text-gray-500 dark:text-gray-400 mb-4">
                ¿Estás seguro que deseas eliminar
                @if($itemName)
                    <span class="font-semibold text-gray-700 dark:text-gray-300">"{{ $itemName }}"</span>
                @endif
                ?
            </p>

            <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-4">
                <p class="text-sm text-red-600 dark:text-red-400">
                    ⚠️ Esta acción eliminará permanentemente al usuario y no se puede deshacer.
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <button @click="open = false"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </button>

                <form action="{{ route($route, $id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors">
                        Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
