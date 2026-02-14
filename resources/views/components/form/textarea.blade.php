@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'helper' => null,
    'errorName' => null,
])

@php
    $id = $id ?? $name;
    $errorName = $errorName ?? $name;
    $hasError = $errors->has($errorName);
    $errorMessage = $errors->first($errorName);

    $baseClasses = 'dark:bg-dark-900 shadow-theme-xs w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';

    $stateClasses = $hasError
        ? 'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800'
        : 'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800';

    $disabledClasses = $disabled ? 'disabled:border-gray-100 disabled:placeholder:text-gray-300 dark:disabled:border-gray-800 dark:disabled:placeholder:text-white/15 disabled:bg-gray-50 dark:disabled:bg-white/[0.03]' : '';
@endphp

@if($label)
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
@endif

<textarea
    name="{{ $name }}"
    id="{{ $id }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $readonly ? 'readonly' : '' }}
    {{ $attributes->merge(['class' => "$baseClasses $stateClasses $disabledClasses"]) }}
>{{ old($name, $value) }}</textarea>

@if($helper && !$hasError)
    <p class="text-theme-xs text-gray-500 dark:text-gray-400 mt-1.5">{{ $helper }}</p>
@endif

@if($hasError)
    <p class="text-theme-xs text-error-500 mt-1.5">{{ $errorMessage }}</p>
@endif
