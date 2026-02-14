@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'checked' => false,
    'value' => '1',
    'disabled' => false,
    'helper' => null,
    'color' => 'brand', // brand, gray
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
    $errorMessage = $errors->first($name);
@endphp

<div x-data="{ switcherToggle: {{ old($name, $checked) ? 'true' : 'false' }} }">
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
                   @change="switcherToggle = !switcherToggle"
                   {{ $disabled ? 'disabled' : '' }}
                   {{ old($name, $checked) ? 'checked' : '' }}
                   {{ $attributes }}>

            <div class="block h-6 w-11 rounded-full transition-colors"
                 :class="{
                     'bg-brand-500 dark:bg-brand-500': switcherToggle && '{{ $color }}' === 'brand',
                     'bg-gray-700 dark:bg-white/10': switcherToggle && '{{ $color }}' === 'gray',
                     'bg-gray-200 dark:bg-white/10': !switcherToggle && !{{ $disabled ? 'true' : 'false' }},
                     'bg-gray-100 dark:bg-gray-800': {{ $disabled ? 'true' : 'false' }}
                 }">
            </div>

            <div class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white transition-transform duration-200 ease-linear"
                 :class="switcherToggle ? 'translate-x-full' : 'translate-x-0'">
            </div>
        </div>

        @if($label)
            <span>{{ $label }}</span>
        @endif
    </label>

    @if($helper && !$hasError)
        <p class="text-theme-xs text-gray-500 dark:text-gray-400 mt-1.5">{{ $helper }}</p>
    @endif

    @if($hasError)
        <p class="text-theme-xs text-error-500 mt-1.5">{{ $errorMessage }}</p>
    @endif
</div>
