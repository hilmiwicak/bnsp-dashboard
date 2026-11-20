@props(['tone' => 'gray'])

@php
    $tones = [
        'green' => 'bg-[#dcfaf8] text-[#0f9b8f]',
        'amber' => 'bg-[#fff5d1] text-[#b57e00]',
        'gray' => 'bg-[#f2f4f7] text-dash-muted',
        'rose' => 'bg-rose-50 text-rose-600',
        'blue' => 'bg-[#e7edff] text-[#396aff]',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-3 py-1 text-[13px] font-semibold '.($tones[$tone] ?? $tones['gray'])]) }}>
    {{ $slot }}
</span>
