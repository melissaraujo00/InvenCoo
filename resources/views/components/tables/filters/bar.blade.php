@props(['action' => null, 'method' => 'GET'])

<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <form action="{{ $action ?? request()->url() }}" method="{{ $method }}" class="flex flex-wrap items-end gap-4">
        {{ $slot }}

        @if(isset($resetButton))
            <div>
                <x-form.button href="{{ $resetButton }}" variant="secondary" size="md">
                    Limpiar filtros
                </x-form.button>
            </div>
        @endif
    </form>
</div>
