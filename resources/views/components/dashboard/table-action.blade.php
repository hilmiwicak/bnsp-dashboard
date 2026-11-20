@props([
    'href' => null,
    'method' => 'GET',
    'tone' => 'neutral',
    'confirm' => null,
])

@php
    $tones = [
        'neutral' => 'border-dash-line text-dash-ink hover:border-dash-primary hover:text-dash-primary',
        'danger' => 'border-rose-200 text-rose-600 hover:border-rose-400 hover:bg-rose-50',
        'primary' => 'border-transparent bg-dash-indigo text-white hover:brightness-110',
    ];

    $classes = 'rounded-full border px-4 py-2 text-[13px] font-medium transition '.($tones[$tone] ?? $tones['neutral']);
@endphp

@if (strtoupper($method) === 'GET')
    <a href="{{ $href }}" class="{{ $classes }}">{{ $slot }}</a>
@else
    <form
        method="POST"
        action="{{ $href }}"
        @if ($confirm) onsubmit="return confirm(@js($confirm))" @endif
    >
        @csrf
        @method($method)

        <button type="submit" class="{{ $classes }}">{{ $slot }}</button>
    </form>
@endif
