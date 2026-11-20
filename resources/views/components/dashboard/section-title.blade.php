@props(['title' => ''])

<div class="mb-5 flex items-center justify-between gap-4">
    <h2 class="text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">{{ $title }}</h2>

    @isset($action)
        {{ $action }}
    @endisset
</div>
