@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'submit',
])

@php
    $variants = [
        'primary' => 'bg-dash-indigo text-white hover:brightness-110',
        'secondary' => 'border border-dash-line bg-white text-dash-ink hover:border-dash-primary hover:text-dash-primary',
        'danger' => 'border border-rose-200 bg-white text-rose-600 hover:border-rose-400 hover:bg-rose-50',
    ];

    $classes = 'inline-flex h-[46px] items-center justify-center gap-2 rounded-full px-6 text-[15px] font-medium transition focus:outline-none focus-visible:ring-4 focus-visible:ring-dash-primary/20 '
        .($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
