@php
    $columns = $columns ?? [];
    $heading = $heading ?? 'Modul';
    $description = $description ?? '';
@endphp

<x-layouts.dashboard :title="$heading" :heading="$heading">
    <div class="space-y-6">
        <section class="rounded-[20px] bg-white shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-dash-line/70 px-6 py-5 lg:px-8">
                <div>
                    <h2 class="text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">{{ $heading }}</h2>
                    <p class="mt-1 max-w-[640px] text-sm text-dash-muted">{{ $description }}</p>
                </div>

                <span class="inline-flex shrink-0 items-center gap-2 rounded-full bg-[#fff5d1] px-4 py-1.5 text-xs font-semibold text-[#b57e00]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#ffbb38]"></span>
                    Tahap berikutnya
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left">
                    <thead>
                        <tr class="border-b border-dash-line/70 bg-dash-surface/60 text-[13px] uppercase tracking-wide text-dash-muted">
                            @foreach ($columns as $column)
                                <th scope="col" class="px-6 py-4 font-medium first:pl-8 last:pr-8">{{ $column }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="{{ max(count($columns), 1) }}" class="px-6 py-16 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-dash-surface text-dash-muted">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                </span>

                                <p class="mt-4 text-[15px] font-medium text-dash-ink">Belum ada data pada modul ini.</p>
                                <p class="mt-1 text-sm text-dash-muted">
                                    Struktur tabel sudah disiapkan. Input data akan tersedia pada pengembangan berikutnya.
                                </p>

                                <div class="mt-5 flex justify-center">
                                    <x-dashboard.button variant="secondary" :href="route('penyewa.index')">Lihat Data Penyewa</x-dashboard.button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.dashboard>
