@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex items-center gap-1.5">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="flex h-9 items-center gap-1 rounded-full border border-dash-line px-3 text-[13px] font-medium text-dash-soft">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 4.5-7.5 7.5 7.5 7.5" />
                </svg>
                Sebelumnya
            </span>
        @else
            <a
                href="{{ $paginator->previousPageUrl() }}"
                rel="prev"
                class="flex h-9 items-center gap-1 rounded-full border border-dash-line px-3 text-[13px] font-medium text-dash-ink transition hover:border-dash-primary hover:text-dash-primary"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 4.5-7.5 7.5 7.5 7.5" />
                </svg>
                Sebelumnya
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="flex h-9 w-9 items-center justify-center text-[13px] text-dash-soft">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="flex h-9 w-9 items-center justify-center rounded-full bg-dash-indigo text-[13px] font-semibold text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $url }}"
                            class="flex h-9 w-9 items-center justify-center rounded-full text-[13px] font-medium text-dash-ink transition hover:bg-dash-surface hover:text-dash-primary"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a
                href="{{ $paginator->nextPageUrl() }}"
                rel="next"
                class="flex h-9 items-center gap-1 rounded-full border border-dash-line px-3 text-[13px] font-medium text-dash-ink transition hover:border-dash-primary hover:text-dash-primary"
            >
                Berikutnya
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 4.5 7.5 7.5L9 19.5" />
                </svg>
            </a>
        @else
            <span class="flex h-9 items-center gap-1 rounded-full border border-dash-line px-3 text-[13px] font-medium text-dash-soft">
                Berikutnya
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 4.5 7.5 7.5L9 19.5" />
                </svg>
            </span>
        @endif
    </nav>
@endif
