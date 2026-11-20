@props([
    'name',
    'type' => 'text',
    'placeholder' => null,
    'icon' => null,
    'error' => null,
])

@php
    $icons = [
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0v.75H4.5v-.75z" />',
        'mail' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5v10.5H3.75V6.75z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5l8.25 5.25L20.25 7.5" />',
        'lock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 10.5V7.5a5.25 5.25 0 0110.5 0v3" /><rect x="4.5" y="10.5" width="15" height="9" rx="2.25" stroke-linecap="round" stroke-linejoin="round" />',
    ];

    $classes = implode(' ', [
        'h-14 w-full rounded-full border bg-white pl-[3.25rem] pr-6 text-[15px] text-neutral-800',
        'shadow-[0_1px_2px_rgba(16,24,40,0.04)] outline-none transition placeholder:text-neutral-400 focus:ring-4',
        $error
            ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-500/10'
            : 'border-neutral-200 focus:border-brand-400 focus:ring-brand-500/10',
    ]);
@endphp

<div>
    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-[3.25rem] items-center justify-center {{ $error ? 'text-rose-400' : 'text-neutral-400' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    {!! $icons[$icon] ?? $icons['user'] !!}
                </svg>
            </span>
        @endif

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            {{ $attributes->merge(['class' => $classes]) }}
        />
    </div>

    @if ($error)
        <p id="{{ $name }}-error" class="mt-1.5 pl-5 text-[13px] font-medium text-rose-600">{{ $error }}</p>
    @endif
</div>
