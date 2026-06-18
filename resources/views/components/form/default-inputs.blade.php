@props(['name', 'label' => '', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-control"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
</div>
