@props([
    'name',
    'type' => 'text',
    'label' => null,
    'placeholder' => null,
    'error' => null,
])

@php
    $classes = implode(' ', [
        'h-[52px] w-full rounded-[10px] border bg-white px-4 text-[15px] text-neutral-900',
        'shadow-[0_1px_2px_rgba(16,24,40,0.04)] outline-none transition placeholder:text-neutral-400 focus:ring-4',
        $error
            ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-500/10'
            : 'border-neutral-300 focus:border-brand-500 focus:ring-brand-500/10',
    ]);
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-2 block text-[14px] font-medium text-neutral-700">
            {{ $label }}
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
        {{ $attributes->merge(['class' => $classes]) }}
    />

    @if ($error)
        <p id="{{ $name }}-error" class="mt-2 text-[13px] font-medium text-rose-600">{{ $error }}</p>
    @endif
</div>
