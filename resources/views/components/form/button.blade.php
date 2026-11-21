@props(['type' => 'submit'])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'flex h-16 w-full items-center justify-center rounded-2xl bg-[linear-gradient(180deg,#8b5cf6_0%,#6f34e0_100%)] text-[16px] font-semibold text-white shadow-[0_18px_32px_-14px_rgba(111,52,224,0.85)] transition hover:brightness-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2',
    ]) }}
>
    {{ $slot }}
</button>
