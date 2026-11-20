@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'error' => null,
])

@php
    $current = (string) old($name, $selected);

    $options = collect($options)->mapWithKeys(
        fn ($label, $value) => [is_int($value) ? $label : $value => $label]
    );

    $base = 'h-[50px] w-full appearance-none rounded-2xl border bg-white pl-4 pr-11 text-[15px] outline-none transition focus:ring-4';

    $tone = $error
        ? 'border-rose-300 text-dash-ink focus:border-rose-400 focus:ring-rose-500/10'
        : 'border-dash-line text-dash-ink focus:border-dash-primary focus:ring-dash-primary/10';
@endphp

<div>
    <label for="{{ $name }}" class="mb-2 block text-[15px] font-medium text-dash-ink">
        {{ $label ?? $name }}@if ($required)<span class="text-dash-alert"> *</span>@endif
    </label>

    <div class="relative">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            class="{{ $base }} {{ $tone }} {{ $current === '' ? 'text-dash-soft' : '' }}"
        >
            @if ($placeholder)
                <option value="" @selected($current === '')>{{ $placeholder }}</option>
            @endif

            @foreach ($options as $value => $label)
                <option value="{{ $value }}" @selected($current === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>

        <svg class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-dash-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </div>

    @if ($error)
        <p id="{{ $name }}-error" class="mt-1.5 text-[13px] font-medium text-rose-600">{{ $error }}</p>
    @endif
</div>
