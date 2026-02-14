@props([
    'name' => '',
    'label' => null,
    'required' => false,
    'helper' => null,
    'errorName' => null,
    'class' => null,
])

@php
    $errorName = $errorName ?? $name;
    $hasError = $errors->has($errorName);
    $errorMessage = $errors->first($errorName);
@endphp

<div class="{{ $class }}">
    @if($label)
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
            @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    {{ $slot }}

    @if($helper && !$hasError)
        <p class="text-theme-xs text-gray-500 dark:text-gray-400 mt-1.5">{{ $helper }}</p>
    @endif

    @if($hasError)
        <p class="text-theme-xs text-error-500 mt-1.5">{{ $errorMessage }}</p>
    @endif
</div>
