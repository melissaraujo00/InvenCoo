@props([
    'id',
    'editRoute' => null,
    'deleteRoute' => null,
    'showEdit' => true,
    'showDelete' => true,
    'customActions' => null
])

<div class="flex justify-end gap-3 text-gray-500 dark:text-gray-400">
    @if($showEdit && $editRoute)
    <a href="{{ route($editRoute, $id) }}"
       class="hover:text-blue-500 transition-colors"
       title="Editar">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                  stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    @endif

    {{ $customActions ?? '' }}

    @if($showDelete && $deleteRoute)
    <form action="{{ route($deleteRoute, $id) }}"
          method="POST"
          onsubmit="return confirm('¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.')"
          class="inline">
        @csrf
        @method('DELETE')
        <button class="hover:text-red-500 transition-colors" title="Eliminar">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </form>
    @endif
</div>
