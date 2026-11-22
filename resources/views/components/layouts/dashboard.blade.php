@props(['title' => null, 'heading' => 'Overview'])

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

                    {{-- Logout selalu terlihat, untuk semua role. --}}
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf

                        <button
                            type="submit"
                            class="flex h-[44px] w-[44px] items-center justify-center gap-2 rounded-full bg-white text-dash-muted shadow-[0_2px_12px_rgba(25,35,80,0.08)] transition hover:text-dash-alert focus:outline-none focus-visible:ring-2 focus-visible:ring-dash-primary/30 sm:h-[50px] sm:w-auto sm:px-5"
                            aria-label="Logout"
                            title="Logout"
                        >
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>

                            <span class="hidden text-[14px] font-medium sm:inline">Logout</span>
                        </button>
                    </form>
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
