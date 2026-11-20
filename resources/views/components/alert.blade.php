@props(['type' => 'success'])

@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'error' => 'border-rose-200 bg-rose-50 text-rose-700',
    ][$type] ?? 'border-neutral-200 bg-neutral-50 text-neutral-700';

    $role = $type === 'error' ? 'alert' : 'status';
@endphp

<div
    role="{{ $role }}"
    {{ $attributes->merge(['class' => 'rounded-2xl border px-4 py-3 text-[15px] '.$styles]) }}
>
    {{ $slot }}
</div>
