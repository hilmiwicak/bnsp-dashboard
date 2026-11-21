@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — '.config('app.name') : config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ Vite::fonts(['poppins']) }}
</head>
<body class="min-h-full bg-white font-sans text-neutral-800">
    <div class="flex min-h-screen flex-col">
        <header class="px-6 pt-8 sm:px-10 lg:px-[7%] lg:pt-10">
            <a
                href="{{ url('/') }}"
                class="inline-flex items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-4"
            >
                <x-logo class="h-9 w-9" />
                <span class="text-[19px] font-semibold tracking-tight text-neutral-900">Kantor Virtual</span>
            </a>
        </header>

        <main class="flex flex-1 items-center px-6 py-12 sm:px-10 lg:px-[7%] lg:py-16">
            <div class="grid w-full items-center gap-16 lg:grid-cols-[minmax(0,430px)_minmax(0,1fr)]">
                <section class="w-full max-w-[430px]">
                    {{ $slot }}
                </section>

                <div class="hidden lg:block">
                    <x-auth.illustration />
                </div>
            </div>
        </main>
    </div>
</body>
</html>
