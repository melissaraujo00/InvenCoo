@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'helper' => null,
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
    $errorMessage = $errors->first($name);
@endphp

<div x-data="{ checkboxChecked: {{ old($name, $checked) ? 'true' : 'false' }} }">
    <label for="{{ $id }}"
           @class([
               'flex cursor-pointer items-center gap-3 text-sm font-medium select-none',
               'text-gray-400 cursor-not-allowed' => $disabled,
               'text-gray-700 dark:text-gray-400' => !$disabled,
           ])>

        <div class="relative">
            <input type="checkbox"
                   name="{{ $name }}"
                   id="{{ $id }}"
                   value="{{ $value }}"
                   class="sr-only"
                   @change="checkboxChecked = !checkboxChecked"
                   {{ $disabled ? 'disabled' : '' }}
                   {{ old($name, $checked) ? 'checked' : '' }}
                   {{ $attributes }}>

            <div class="flex h-5 w-5 items-center justify-center rounded border transition-colors"
                 :class="{
                     'border-brand-500 bg-brand-500': checkboxChecked && !{{ $disabled ? 'true' : 'false' }},
                     'border-gray-300 dark:border-gray-700 bg-transparent': !checkboxChecked && !{{ $disabled ? 'true' : 'false' }},
                     'border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800': {{ $disabled ? 'true' : 'false' }}
                 }">
                <svg x-show="checkboxChecked"
                     class="h-3.5 w-3.5 text-white"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        @if($label)
            <span>{{ $label }}</span>
        @endif
    </label>

    @if($helper && !$hasError)
        <p class="text-theme-xs text-gray-500 dark:text-gray-400 mt-1.5 ml-7">{{ $helper }}</p>
    @endif

    @if($hasError)
        <p class="text-theme-xs text-error-500 mt-1.5 ml-7">{{ $errorMessage }}</p>
    @endif
</div>
