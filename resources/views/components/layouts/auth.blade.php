@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — '.config('app.name') : config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ Vite::fonts() }}
</head>
<body class="min-h-full bg-white font-sans text-neutral-800">
    <main class="grid min-h-screen grid-cols-1 lg:grid-cols-[59fr_41fr]">
        <x-auth.brand-panel />

        <section class="flex items-center justify-center px-6 py-16 sm:px-10 lg:py-0">
            <div class="w-full max-w-[340px]">
                {{ $slot }}
            </div>
        </section>
    </main>
</body>
</html>
