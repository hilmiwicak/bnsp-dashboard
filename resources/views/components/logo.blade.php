@props(['class' => 'h-9 w-9'])

{{-- Logo mark: cloud + monitor, phone and clock on a violet tile. --}}
<svg
    {{ $attributes->merge(['class' => $class.' shrink-0']) }}
    viewBox="0 0 64 64"
    fill="none"
    aria-hidden="true"
>
    <rect width="64" height="64" rx="13" fill="#A79AF0" />

    <g transform="translate(0 1.5)">
        {{-- Cloud --}}
        <g fill="#ffffff">
            <circle cx="17.5" cy="22.5" r="8.5" />
            <circle cx="32" cy="16" r="11" />
            <circle cx="46" cy="22" r="7" />
            <rect x="11" y="19" width="42" height="13" rx="6.5" />
        </g>

        {{-- Monitor --}}
        <rect x="17" y="25" width="30" height="21.5" rx="3.5" fill="#ffffff" />
        <rect x="20.5" y="28.5" width="23" height="14.5" rx="2.6" fill="#A79AF0" />
        <rect x="15.5" y="46.5" width="33" height="4.5" rx="2.25" fill="#ffffff" />
        <rect x="29" y="48.1" width="6" height="1.3" rx="0.65" fill="#A79AF0" />

        {{-- Phone --}}
        <rect x="8" y="30" width="10" height="21" rx="2.6" fill="#ffffff" />
        <rect x="10.2" y="32.4" width="5.6" height="14.6" rx="1.3" fill="#A79AF0" />

        {{-- Clock --}}
        <circle cx="52" cy="38" r="7" fill="#ffffff" />
        <circle cx="52" cy="38" r="4.6" fill="#A79AF0" />
        <path
            d="M54.1 35.3 50.6 38 54.1 40.7"
            stroke="#ffffff"
            stroke-width="2.1"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </g>
</svg>
