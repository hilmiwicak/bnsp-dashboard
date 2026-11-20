@props([
    'variant' => 'primary',
    'balance' => 'Rp5.756.000',
    'holder' => 'Budi Santoso',
    'valid' => '12/26',
    'number' => '3778 **** **** 1234',
])

@php
    $primary = $variant === 'primary';

    $frame = $primary
        ? 'bg-[linear-gradient(135deg,#3b37ea_0%,#2b23d6_100%)] text-white'
        : 'border border-dash-line bg-white text-dash-ink';

    $label = $primary ? 'text-white/70' : 'text-dash-muted';
    $divider = $primary ? 'border-white/20' : 'border-dash-line';
@endphp

<div class="flex min-h-[215px] flex-col justify-between rounded-[20px] p-6 {{ $frame }}">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs {{ $label }}">Saldo</p>
            <p class="mt-1 text-xl font-semibold">{{ $balance }}</p>
        </div>

        <svg class="h-7 w-9 {{ $primary ? 'text-white' : 'text-dash-ink' }}" viewBox="0 0 34 26" fill="none" aria-hidden="true">
            <rect x="0.75" y="0.75" width="32.5" height="24.5" rx="5" stroke="currentColor" stroke-opacity="0.55" stroke-width="1.5" />
            <path d="M12 1v24M22 1v24M1 9h32M1 17h32" stroke="currentColor" stroke-opacity="0.35" stroke-width="1.5" />
        </svg>
    </div>

    <div class="flex items-center gap-10 pt-6">
        <div>
            <p class="text-[10px] font-medium uppercase tracking-wider {{ $label }}">Pemegang Kartu</p>
            <p class="mt-1 text-[15px] font-semibold">{{ $holder }}</p>
        </div>
        <div>
            <p class="text-[10px] font-medium uppercase tracking-wider {{ $label }}">Berlaku s.d.</p>
            <p class="mt-1 text-[15px] font-semibold">{{ $valid }}</p>
        </div>
    </div>

    <div class="mt-5 flex items-center justify-between border-t pt-4 {{ $divider }}">
        <p class="text-[19px] font-semibold tracking-wide">{{ $number }}</p>

        <div class="flex items-center">
            <span class="h-6 w-6 rounded-full {{ $primary ? 'bg-white/40' : 'bg-[#9199af]/40' }}"></span>
            <span class="-ml-2.5 h-6 w-6 rounded-full {{ $primary ? 'bg-white/70' : 'bg-[#9199af]/70' }}"></span>
        </div>
    </div>
</div>
