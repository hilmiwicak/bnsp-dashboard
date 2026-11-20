@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'error' => null,
    'hint' => null,
])

@php
    $current = old($name, $value);

    $base = 'w-full rounded-2xl border bg-white px-4 text-[15px] text-dash-ink outline-none transition placeholder:text-dash-soft focus:ring-4';

    $tone = $error
        ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-500/10'
        : 'border-dash-line focus:border-dash-primary focus:ring-dash-primary/10';
@endphp

<div>
    <label for="{{ $name }}" class="mb-2 block text-[15px] font-medium text-dash-ink">
        {{ $label ?? $name }}@if ($required)<span class="text-dash-alert"> *</span>@endif
    </label>

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $attributes->get('rows', 3) }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            class="{{ $base }} {{ $tone }} py-3"
        >{{ $current }}</textarea>
    @else
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $current }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            class="{{ $base }} {{ $tone }} h-[50px]"
        />
    @endif

    @if ($error)
        <p id="{{ $name }}-error" class="mt-1.5 text-[13px] font-medium text-rose-600">{{ $error }}</p>
    @elseif ($hint)
        <p class="mt-1.5 text-[13px] text-dash-muted">{{ $hint }}</p>
    @endif
</div>
