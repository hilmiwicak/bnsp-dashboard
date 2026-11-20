@php
    $isFiltered = $filters['q'] !== '' || $filters['status'] || $filters['room'];
@endphp

<x-layouts.dashboard title="Pemesanan" heading="Pemesanan">
    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success">{{ session('status') }}</x-alert>
        @endif

        <x-dashboard.data-table
            title="Daftar Pemesanan"
            :subtitle="$totalSemua.' pemesanan ruang tercatat.'"
            :paginator="$bookings"
            unit="pemesanan"
        >
            <x-slot:action>
                <x-dashboard.button :href="route('pemesanan.create')" class="shrink-0">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Pemesanan
                </x-dashboard.button>
            </x-slot:action>

            <x-slot:filters>
                <form method="GET" action="{{ route('pemesanan.index') }}" class="flex flex-wrap items-end gap-3 px-6 py-5 lg:px-8">
                    <div class="min-w-[220px] flex-1">
                        <label for="q" class="mb-2 block text-[15px] font-medium text-dash-ink">Pencarian</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-dash-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="11" cy="11" r="7.25" />
                                <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                            </svg>
                            <input
                                id="q"
                                name="q"
                                type="search"
                                value="{{ $filters['q'] }}"
                                placeholder="Cari kode, tenant, atau ruang"
                                class="h-[50px] w-full rounded-2xl border border-dash-line bg-white pl-11 pr-4 text-[15px] text-dash-ink outline-none transition placeholder:text-dash-soft focus:border-dash-primary focus:ring-4 focus:ring-dash-primary/10"
                            />
                        </div>
                    </div>

                    <div class="w-full sm:w-[210px]">
                        <x-dashboard.form-select
                            name="room"
                            label="Ruang"
                            :options="$rooms"
                            :selected="$filters['room']"
                            placeholder="Semua Ruang"
                        />
                    </div>

                    <div class="w-full sm:w-[170px]">
                        <x-dashboard.form-select
                            name="status"
                            label="Status"
                            :options="\App\Models\Booking::STATUS"
                            :selected="$filters['status']"
                            placeholder="Semua Status"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <x-dashboard.button type="submit">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                            </svg>
                            Cari
                        </x-dashboard.button>

                        @if ($isFiltered)
                            <x-dashboard.button variant="secondary" :href="route('pemesanan.index')">Reset</x-dashboard.button>
                        @endif
                    </div>
                </form>
            </x-slot:filters>

            <table class="w-full min-w-[1080px] text-left">
                <thead>
                    <tr class="border-y border-dash-line/70 bg-dash-surface/60 text-[13px] uppercase tracking-wide text-dash-muted">
                        <th scope="col" class="px-6 py-4 font-medium lg:px-8">Kode</th>
                        <th scope="col" class="px-6 py-4 font-medium">Tenant</th>
                        <th scope="col" class="px-6 py-4 font-medium">Ruang</th>
                        <th scope="col" class="px-6 py-4 font-medium">Jadwal</th>
                        <th scope="col" class="px-6 py-4 font-medium">Peserta</th>
                        <th scope="col" class="px-6 py-4 font-medium">Status</th>
                        <th scope="col" class="px-6 py-4 text-right font-medium lg:px-8">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-dash-line/70">
                    @forelse ($bookings as $booking)
                        <tr class="align-middle transition hover:bg-dash-surface/60">
                            <td class="whitespace-nowrap px-6 py-4 text-[15px] font-semibold text-dash-ink lg:px-8">
                                {{ $booking->kode_pemesanan }}
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-[15px] font-medium text-dash-ink">{{ $booking->tenant->nama_perusahaan }}</p>
                                <p class="text-[13px] text-dash-muted">{{ $booking->tenant->nama_pic }}</p>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-[15px] text-dash-ink">{{ $booking->room->nama_ruang }}</p>
                                <p class="text-[13px] text-dash-muted">{{ $booking->room->kode_ruang }}</p>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                {{ $booking->tanggal->format('d/m/Y') }}
                                <span class="block text-[13px] text-dash-muted">{{ $booking->rentang() }}</span>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                {{ $booking->jumlah_peserta }} orang
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <x-dashboard.badge :tone="$booking->statusTone()">{{ $booking->status }}</x-dashboard.badge>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right lg:px-8">
                                <div class="inline-flex items-center gap-2">
                                    @if ($booking->status === 'Menunggu')
                                        <form method="POST" action="{{ route('pemesanan.status', $booking) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Disetujui">

                                            <button
                                                type="submit"
                                                class="rounded-full border border-transparent bg-dash-indigo px-4 py-2 text-[13px] font-medium text-white transition hover:brightness-110"
                                            >
                                                Setujui
                                            </button>
                                        </form>
                                    @endif

                                    <x-dashboard.table-action :href="route('pemesanan.edit', $booking)">Edit</x-dashboard.table-action>

                                    <x-dashboard.table-action
                                        :href="route('pemesanan.destroy', $booking)"
                                        method="DELETE"
                                        tone="danger"
                                        confirm="Hapus pemesanan ini?"
                                    >
                                        Hapus
                                    </x-dashboard.table-action>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-dash-surface text-dash-muted">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5M8.25 10.5h7.5M8.25 14.25h4.5M6 2.25h12A1.875 1.875 0 0 1 19.875 4.125v16.5l-2.625-1.5-2.625 1.5-2.625-1.5-2.625 1.5-2.625-1.5V4.125A1.875 1.875 0 0 1 6 2.25Z" />
                                    </svg>
                                </span>

                                <p class="mt-4 text-[15px] font-medium text-dash-ink">
                                    {{ $isFiltered ? 'Tidak ada pemesanan yang cocok dengan pencarian.' : 'Belum ada pemesanan.' }}
                                </p>
                                <p class="mt-1 text-[13px] text-dash-muted">
                                    {{ $isFiltered ? 'Coba ubah kata kunci atau filter yang digunakan.' : 'Catat pemesanan pertama untuk mulai mengelola penggunaan ruang.' }}
                                </p>

                                <div class="mt-5 flex justify-center">
                                    @if ($isFiltered)
                                        <x-dashboard.button variant="secondary" :href="route('pemesanan.index')">Reset Filter</x-dashboard.button>
                                    @else
                                        <x-dashboard.button :href="route('pemesanan.create')">Tambah Pemesanan</x-dashboard.button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-dashboard.data-table>
    </div>
</x-layouts.dashboard>
