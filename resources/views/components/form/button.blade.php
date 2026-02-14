@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, success
    'size' => 'md', // sm, md, lg
    'href' => null,
    'loading' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-colors rounded-lg';

    $variants = [
        'primary' => 'bg-brand-500 text-white hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600',
        'secondary' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 dark:bg-red-500 dark:hover:bg-red-600',
        'success' => 'bg-green-500 text-white hover:bg-green-600 dark:bg-green-500 dark:hover:bg-green-600',
    ];

    $sizes = [
        'sm' => 'px-3 py-2 text-xs gap-1.5',
        'md' => 'px-4 py-3 text-sm gap-2',
        'lg' => 'px-6 py-4 text-base gap-2.5',
    ];

    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
            {{ $attributes->merge(['class' => $classes]) }}
            @if($loading) wire:loading.attr="disabled" @endif>
        @if($loading)
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
