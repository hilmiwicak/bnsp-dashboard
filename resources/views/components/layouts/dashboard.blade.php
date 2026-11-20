@props(['title' => null, 'heading' => 'Overview'])

@php
    $initials = collect(explode(' ', (string) auth()->user()?->name))
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — '.config('app.name') : config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ Vite::fonts(['inter']) }}
</head>
<body class="min-h-full bg-dash-surface font-inter text-dash-ink">
    <div class="flex min-h-screen">
        <x-dashboard.sidebar />

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-black/[0.03] bg-white px-4 py-4 sm:px-6 lg:px-10 lg:py-5">
                <button
                    type="button"
                    data-sidebar-toggle
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-dash-ink transition hover:bg-dash-surface focus:outline-none focus-visible:ring-2 focus-visible:ring-dash-primary/40 lg:hidden"
                    aria-label="Buka navigasi"
                >
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                </button>

                <h1 class="truncate text-[24px] font-bold leading-tight tracking-tight text-dash-ink sm:text-[28px]">
                    {{ $heading }}
                </h1>

                <div class="ml-auto flex items-center gap-2.5 sm:gap-4 lg:gap-6">
                    <div class="relative hidden md:block">
                        <svg class="pointer-events-none absolute left-5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-dash-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="11" cy="11" r="7.25" />
                            <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                        </svg>
                        <input
                            type="search"
                            placeholder="Cari sesuatu"
                            aria-label="Cari sesuatu"
                            class="h-[50px] w-[230px] rounded-full bg-dash-surface pl-14 pr-6 text-[15px] text-dash-ink outline-none transition placeholder:text-dash-soft focus:ring-2 focus:ring-dash-primary/20 lg:w-[260px]"
                        />
                    </div>

                    <button
                        type="button"
                        class="hidden h-[50px] w-[50px] items-center justify-center rounded-full bg-white text-dash-muted shadow-[0_2px_12px_rgba(25,35,80,0.08)] transition hover:text-dash-primary sm:flex"
                        aria-label="Pengaturan"
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="relative flex h-[50px] w-[50px] items-center justify-center rounded-full bg-white text-dash-muted shadow-[0_2px_12px_rgba(25,35,80,0.08)] transition hover:text-dash-primary"
                        aria-label="Notifikasi"
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span class="absolute right-[11px] top-[9px] h-2 w-2 rounded-full bg-[#fe5c73] ring-2 ring-white"></span>
                    </button>

                    <div class="flex h-[44px] w-[44px] items-center justify-center rounded-full bg-[#dfe7f5] text-[13px] font-semibold text-dash-ink sm:h-[52px] sm:w-[52px] lg:h-[60px] lg:w-[60px] lg:text-[15px]">
                        {{ $initials ?: 'KV' }}
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <div data-sidebar-backdrop class="fixed inset-0 z-30 hidden bg-black/30 lg:hidden"></div>
</body>
</html>
