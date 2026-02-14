@props([
    'name' => '',
    'id' => null,
    'value' => '',
    'label' => '',
    'checked' => false,
    'disabled' => false,
])

@php
    $id = $id ?? $name . '_' . $value;
@endphp

<label for="{{ $id }}"
    @class([
        'relative flex cursor-pointer select-none items-center gap-3 text-sm font-medium',
        'text-gray-300 dark:text-gray-600 cursor-not-allowed' => $disabled,
        'text-gray-700 dark:text-gray-400' => !$disabled,
    ])>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="radio"
        value="{{ $value }}"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="sr-only"
        {{ $attributes }}
    />

    <span @class([
        'flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] transition-colors',
        'border-brand-500 bg-brand-500' => old($name, $checked) && !$disabled,
        'bg-transparent border-gray-300 dark:border-gray-700 hover:border-brand-500 dark:hover:border-brand-500' => !old($name, $checked) && !$disabled,
        'bg-gray-100 dark:bg-gray-700 border-gray-200 dark:border-gray-700' => $disabled,
    ])>
        <span @class([
            'h-2 w-2 rounded-full bg-white',
            'block' => old($name, $checked),
            'hidden' => !old($name, $checked),
        ])></span>
    </span>

    {{ $label }}
</label>
