@props([
    'title' => null,
    'headers' => [],
    'rows' => [],
    'searchable' => true,
    'paginator' => null,
    'emptyMessage' => 'No se encontraron registros.',
    'createRoute' => null,
    'createButtonText' => 'Agregar Nuevo',
    'showActions' => true,
    'actions' => null,
    'showFilters' => false, // nueva bandera para mostrar slot de filtros
])

<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
    @if($title || $searchable || $createRoute || $showFilters)
    <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        @if($title)
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h3>
        </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            {{-- Buscador simple --}}
            @if($searchable)
                <x-tables.filters.search />
            @endif

            {{-- Slot para filtros adicionales (se muestra siempre, pero se puede condicionar) --}}
            @if(isset($filters))
                {{ $filters }}
            @endif

            @if($createRoute)
            <a href="{{ $createRoute }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-colors">
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M10.8333 5V9.16667H15V10.8333H10.8333V15H9.16667V10.8333H5V9.16667H9.16667V5H10.8333Z" />
                </svg>
                {{ $createButtonText }}
            </a>
            @endif
        </div>
    </div>
    @endif

    <div class="overflow-hidden">
        <div class="max-w-full px-5 overflow-x-auto custom-scrollbar">
            <table class="min-w-full">
                @if(!empty($headers))
                    <thead>
                        <tr class="border-gray-200 border-y dark:border-gray-700">
                            @foreach($headers as $header)
                                <th class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                                    {{ $header }}
                                </th>
                            @endforeach
                            @if($showActions)
                                <th class="px-4 py-3 font-normal text-right text-gray-500 text-theme-sm dark:text-gray-400">
                                    Acciones
                                </th>
                            @endif
                        </tr>
                    </thead>
                @endif

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    {{ $slot }}

                    @if($slot->isEmpty())
                        <tr>
                            <td colspan="{{ count($headers) + ($showActions ? 1 : 0) }}"
                                class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @if($paginator && $paginator->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
        {{ $paginator->links() }}
    </div>
    @endif
</div>
