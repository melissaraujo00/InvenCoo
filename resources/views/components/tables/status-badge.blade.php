@props([
    'status' => true,
    'activeText' => 'Activo',
    'inactiveText' => 'Inactivo',
    'activeClass' => 'bg-green-100 text-green-800 dark:bg-green-500/15 dark:text-green-500',
    'inactiveClass' => 'bg-red-100 text-red-800 dark:bg-red-500/15 dark:text-red-500'
])

@if($status)
    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $activeClass }}">
        {{ $activeText }}
    </span>
@else
    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $inactiveClass }}">
        {{ $inactiveText }}
    </span>
@endif
