@props(['type' => 'submit'])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'flex h-[52px] w-full items-center justify-center rounded-[10px] bg-brand-600 text-[15px] font-semibold text-white transition hover:bg-brand-700 active:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2',
    ]) }}
>
    {{ $slot }}
</button>
