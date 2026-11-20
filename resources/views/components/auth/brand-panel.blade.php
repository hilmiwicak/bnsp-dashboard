@props([
    'title' => 'Welcome to Kantor Virtual',
    'description' => 'The most “virtual” office in the world.',
])

<section
    class="relative isolate flex min-h-[380px] items-center overflow-hidden bg-[linear-gradient(180deg,var(--color-brand-500)_0%,var(--color-brand-700)_55%,var(--color-brand-900)_100%)] px-8 py-16 text-white sm:px-14 lg:min-h-screen lg:px-[11%]"
>
    {{-- Soft highlight bleeding in from the top-left corner --}}
    <div class="pointer-events-none absolute -left-1/4 -top-1/3 h-[75%] w-[75%] rounded-full bg-[radial-gradient(circle_at_center,rgba(93,166,255,0.45),transparent_65%)]"></div>

    {{-- Outlined arcs anchored off-canvas at the bottom-left --}}
    <svg
        class="pointer-events-none absolute inset-0 h-full w-full"
        viewBox="0 0 1000 1000"
        preserveAspectRatio="xMinYMax slice"
        fill="none"
        aria-hidden="true"
    >
        <circle cx="300" cy="1400" r="470" stroke="white" stroke-opacity="0.14" stroke-width="1.5" />
        <circle cx="300" cy="1400" r="610" stroke="white" stroke-opacity="0.11" stroke-width="1.5" />
        <circle cx="300" cy="1400" r="750" stroke="white" stroke-opacity="0.08" stroke-width="1.5" />
    </svg>

    <div class="relative max-w-xl">
        <h1 class="text-4xl font-bold leading-[1.15] tracking-tight sm:text-5xl">
            {{ $title }}
        </h1>

        <p class="mt-3 text-base font-medium text-white/90 sm:text-lg">
            {{ $description }}
        </p>

        <a
            href="#"
            class="mt-8 inline-flex items-center justify-center rounded-full border border-white/25 bg-[linear-gradient(180deg,#3d93ff_0%,#1877f2_100%)] px-8 py-3 text-[15px] font-medium text-white shadow-[0_12px_30px_-10px_rgba(8,70,190,0.95)] transition hover:brightness-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-brand-700"
        >
            Read More
        </a>
    </div>
</section>
