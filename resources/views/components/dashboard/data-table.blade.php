@props([
    'title',
    'subtitle' => null,
    'paginator' => null,
    'unit' => 'data',
])

<section class="rounded-[20px] bg-white shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-dash-line/70 px-6 py-5 lg:px-8">
        <div>
            <h2 class="text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">{{ $title }}</h2>

            @if ($subtitle)
                <p class="mt-1 text-[13px] text-dash-muted">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($action)
            {{ $action }}
        @endisset
    </div>

    @isset($filters)
        {{ $filters }}
    @endisset

    <div class="overflow-x-auto">
        {{ $slot }}
    </div>

    @if ($paginator && $paginator->total() > 0)
        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-dash-line/70 px-6 py-4 lg:px-8">
            <p class="text-[13px] text-dash-muted">
                Menampilkan {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} dari {{ $paginator->total() }} {{ $unit }}
            </p>

            {{ $paginator->links() }}
        </div>
    @endif
</section>
