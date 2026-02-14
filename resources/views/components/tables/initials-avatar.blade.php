@props(['name', 'lastName', 'id', 'size' => 9, 'showId' => true])

<div class="flex items-center">
    <div class="flex h-{{ $size }} w-{{ $size }} items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 font-semibold text-sm">
        {{ strtoupper(substr($name, 0, 1) . substr($lastName, 0, 1)) }}
    </div>
    <div class="ml-4">
        <div class="text-sm font-medium text-gray-900 dark:text-white">
            {{ $name }} {{ $lastName }}
        </div>
        @if($showId)
        <div class="text-xs text-gray-500 dark:text-gray-400">
            ID: #{{ $id }}
        </div>
        @endif
    </div>
</div>
