@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'users',
    'tone' => 'violet',
])

@php
    $tones = [
        'violet' => 'bg-[#ece7fe] text-[#6f34e0]',
        'indigo' => 'bg-[#e7dffd] text-[#7c3aed]',
        'teal' => 'bg-[#dcfaf8] text-[#0f9b8f]',
        'amber' => 'bg-[#fff5d1] text-[#b57e00]',
        'rose' => 'bg-rose-50 text-rose-600',
    ];

    $icons = [
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />',
        'banknotes' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-4 rounded-[20px] bg-white p-5 shadow-[0_4px_20px_rgba(25,35,80,0.03)]']) }}>
    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $tones[$tone] ?? $tones['violet'] }}">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            {!! $icons[$icon] ?? $icons['users'] !!}
        </svg>
    </span>

    <div class="min-w-0">
        <p class="truncate text-[13px] text-dash-muted">{{ $label }}</p>
        <p class="mt-0.5 truncate text-[21px] font-semibold leading-tight tracking-tight text-dash-ink">{{ $value }}</p>

        @if ($hint)
            <p class="mt-0.5 truncate text-[12px] text-dash-soft">{{ $hint }}</p>
        @endif
    </div>
</div>
