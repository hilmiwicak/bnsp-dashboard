@props(['type' => 'submit'])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'flex h-14 w-full items-center justify-center rounded-full bg-[linear-gradient(180deg,#1180ff_0%,#0a5ce0_100%)] text-sm font-medium text-white shadow-[0_16px_30px_-14px_rgba(10,92,224,0.9)] transition hover:brightness-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2',
    ]) }}
>
    {{ $slot }}
</button>
