@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'value' => null,
    'label' => null,
    'placeholder' => '',
    'icon' => null, // Nombre del icono o HTML
    'iconPosition' => 'left', // left o right
    'prefix' => null, // Texto antes (http://, $, etc)
    'suffix' => null, // Texto después (.com, kg, etc)
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'helper' => null,
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
    $errorMessage = $errors->first($name);

    $baseClasses = 'dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border bg-transparent text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';

    $stateClasses = $hasError
        ? 'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800'
        : 'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800';

    $paddingLeft = $icon && $iconPosition === 'left' ? 'pl-[62px]' : ($prefix ? 'pl-[90px]' : 'pl-4');
    $paddingRight = $icon && $iconPosition === 'right' ? 'pr-[62px]' : ($suffix ? 'pr-[90px]' : 'pr-4');
@endphp

@if($label)
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
@endif

<div class="relative">
    {{-- Icono izquierdo --}}
    @if($icon && $iconPosition === 'left')
        <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
            {!! $icon !!}
        </span>
    @endif

    {{-- Prefijo texto --}}
    @if($prefix)
        <span class="absolute top-1/2 left-0 inline-flex h-11 -translate-y-1/2 items-center justify-center border-r border-gray-200 py-3 pr-3 pl-3.5 text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
            {{ $prefix }}
        </span>
    @endif

    {{-- Icono derecho --}}
    @if($icon && $iconPosition === 'right')
        <span class="absolute top-1/2 right-0 -translate-y-1/2 border-l border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
            {!! $icon !!}
        </span>
    @endif

    {{-- Sufijo texto --}}
    @if($suffix)
        <span class="absolute top-1/2 right-0 inline-flex h-11 -translate-y-1/2 items-center justify-center border-l border-gray-200 py-3 pr-3 pl-3.5 text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
            {{ $suffix }}
        </span>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        class="{{ $baseClasses }} {{ $stateClasses }} {{ $paddingLeft }} {{ $paddingRight }}"
    />
</div>

@if($helper && !$hasError)
    <p class="text-theme-xs text-gray-500 dark:text-gray-400 mt-1.5">{{ $helper }}</p>
@endif

@if($hasError)
    <p class="text-theme-xs text-error-500 mt-1.5">{{ $errorMessage }}</p>
@endif
