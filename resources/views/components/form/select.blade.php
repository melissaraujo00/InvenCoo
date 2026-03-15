@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => 'Seleccionar opción',
    'required' => false,
    'disabled' => false,
    'helper' => null,
    'errorName' => null,
    'searchable' => false, // nueva propiedad
])

@php
    $id = $id ?? $name;
    $errorName = $errorName ?? $name;
    $hasError = $errors->has($errorName);
    $errorMessage = $errors->first($errorName);

    $baseClasses = 'dark:bg-dark-900 shadow-theme-xs h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';

    $stateClasses = $hasError
        ? 'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800'
        : 'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800';

    $disabledClasses = $disabled ? 'disabled:border-gray-100 disabled:placeholder:text-gray-300 dark:disabled:border-gray-800 dark:disabled:placeholder:text-white/15' : '';

    $selectedValue = old($name, $value);
@endphp

@if($searchable)
    {{-- === COMPONENTE CON BÚSQUEDA (combobox) === --}}
    <div
        x-data="{
            options: {{ Js::from($options) }},
            selectedValue: '{{ $selectedValue }}',
            search: '',
            open: false,
            selectedLabel: '',
            init() {
                this.selectedLabel = this.options[this.selectedValue] || '';
            },
            get filteredOptions() {
                if (this.search === '') return this.options;
                const searchLower = this.search.toLowerCase();
                return Object.fromEntries(
                    Object.entries(this.options).filter(([value, label]) =>
                        label.toLowerCase().includes(searchLower)
                    )
                );
            },
            toggle() {
                if (!{{ $disabled ? 'true' : 'false' }}) {
                    this.open = !this.open;
                    if (this.open) this.search = '';
                }
            },
            select(value, label) {
                this.selectedValue = value;
                this.selectedLabel = label;
                this.open = false;
                this.search = '';
            }
        }"
        class="relative z-20 bg-transparent"
    >
        @if($label)
            <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                {{ $label }}
                @if($required) <span class="text-red-500">*</span> @endif
            </label>
        @endif

        {{-- Trigger: muestra el valor seleccionado o placeholder --}}
        <div
            @click="toggle()"
            :class="{
                'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800': {{ $hasError ? 'true' : 'false' }},
                'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800': {{ !$hasError ? 'true' : 'false' }},
                'opacity-50 pointer-events-none': {{ $disabled ? 'true' : 'false' }}
            }"
            class="{{ $baseClasses }} relative flex cursor-pointer items-center justify-between pr-11"
        >
            <span x-text="selectedLabel || '{{ $placeholder }}'" class="truncate"></span>
            <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </div>

        {{-- Dropdown con búsqueda y opciones --}}
        <div
            x-show="open"
            @click.away="open = false"
            x-transition
            class="absolute left-0 right-0 z-50 mt-1 max-h-60 overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900"
            style="display: none;"
        >
            {{-- Input de búsqueda --}}
            <div class="sticky top-0 bg-white p-2 dark:bg-gray-900">
                <input
                    type="text"
                    x-model="search"
                    placeholder="Buscar..."
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"
                >
            </div>

            {{-- Lista de opciones filtradas --}}
            <div class="p-1">
                <template x-for="(label, value) in filteredOptions" :key="value">
                    <div
                        @click="select(value, label)"
                        :class="{ 'bg-brand-50 dark:bg-brand-800/30': selectedValue == value }"
                        class="cursor-pointer rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-brand-50 dark:text-gray-300 dark:hover:bg-brand-800/30"
                        x-text="label"
                    ></div>
                </template>

                {{-- Mensaje si no hay resultados --}}
                <div x-show="Object.keys(filteredOptions).length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    No se encontraron opciones
                </div>
            </div>
        </div>

        {{-- Hidden input para enviar el valor seleccionado --}}
        <input type="hidden" name="{{ $name }}" :value="selectedValue" {{ $required ? 'required' : '' }}>

        @if($helper && !$hasError)
            <p class="text-theme-xs text-gray-500 dark:text-gray-400 mt-1.5">{{ $helper }}</p>
        @endif
    </div>
@else
    {{-- === COMPONENTE ORIGINAL (select nativo) === --}}
    <div x-data="{
        selectedValue: '{{ $selectedValue }}',
        init() {
            this.selectedValue = '{{ $selectedValue }}';
        }
    }" class="relative z-20 bg-transparent">
        @if($label)
            <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                {{ $label }}
                @if($required) <span class="text-red-500">*</span> @endif
            </label>
        @endif

        <div class="relative">
            <select
                name="{{ $name }}"
                id="{{ $id }}"
                x-model="selectedValue"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->merge(['class' => "$baseClasses $stateClasses $disabledClasses"]) }}
            >
                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">{{ $placeholder }}</option>
                @foreach($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}"
                            class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                            {{ $selectedValue == $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>

            <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </div>

        @if($helper && !$hasError)
            <p class="text-theme-xs text-gray-500 dark:text-gray-400 mt-1.5">{{ $helper }}</p>
        @endif
    </div>
@endif
