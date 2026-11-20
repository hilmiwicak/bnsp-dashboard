@php
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $formatTanggal = function (string $tanggal) use ($hari, $bulan): string {
        $date = \Illuminate\Support\Carbon::parse($tanggal);

        return $hari[$date->dayOfWeek].', '.$date->day.' '.$bulan[$date->month].' '.$date->year;
    };

    $hariIni = now()->toDateString();
    $isFiltered = (bool) $filters['room'] || (bool) $filters['tanggal'];
@endphp

<x-layouts.dashboard title="Jadwal Penggunaan Ruang" heading="Jadwal Penggunaan Ruang">
    <div class="space-y-6">
        {{-- Ringkasan jadwal --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex items-center gap-4 rounded-[20px] bg-white p-5 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#e7edff] text-[#396aff]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="text-[13px] text-dash-muted">Total Jadwal</p>
                    <p class="mt-0.5 text-[24px] font-semibold leading-tight text-dash-ink">{{ $totalJadwal }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-[20px] bg-white p-5 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#dcfaf8] text-[#0f9b8f]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </span>

                <div class="min-w-0">
                    <p class="text-[13px] text-dash-muted">Ruang Terpakai</p>
                    <p class="mt-0.5 text-[24px] font-semibold leading-tight text-dash-ink">{{ $totalRuang }}</p>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <section class="rounded-[20px] bg-white shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-dash-line/70 px-6 py-5 lg:px-8">
                <div>
                    <h2 class="text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">Jadwal Penggunaan Ruang</h2>
                    <p class="mt-1 text-[13px] text-dash-muted">
                        Jadwal dibentuk otomatis dari pemesanan yang sudah disetujui. Ubah jadwal melalui modul Pemesanan.
                    </p>
                </div>

                <x-dashboard.button variant="secondary" :href="route('pemesanan.index')" class="shrink-0">
                    Kelola Pemesanan
                </x-dashboard.button>
            </div>

            <form method="GET" action="{{ route('jadwal-ruang.index') }}" class="flex flex-wrap items-end gap-3 px-6 py-5 lg:px-8">
                <div class="w-full sm:w-[280px]">
                    <x-dashboard.form-select
                        name="room"
                        label="Ruang"
                        :options="$rooms"
                        :selected="$filters['room']"
                        placeholder="Semua Ruang"
                    />
                </div>

                <div class="w-full sm:w-[220px]">
                    <label for="tanggal" class="mb-2 block text-[15px] font-medium text-dash-ink">Tanggal</label>

                    <div class="relative">
                        <input
                            id="tanggal"
                            name="tanggal"
                            type="date"
                            value="{{ $filters['tanggal'] }}"
                            class="h-[50px] w-full rounded-2xl border border-dash-line bg-white px-4 text-[15px] text-dash-ink outline-none transition focus:border-dash-primary focus:ring-4 focus:ring-dash-primary/10"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-dashboard.button type="submit">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                        </svg>
                        Tampilkan
                    </x-dashboard.button>

                    @if ($isFiltered)
                        <x-dashboard.button variant="secondary" :href="route('jadwal-ruang.index')">Reset</x-dashboard.button>
                    @endif
                </div>

                <p class="w-full text-[13px] text-dash-muted sm:ml-auto sm:w-auto">
                    {{ $filters['tanggal'] ? 'Menampilkan jadwal pada tanggal terpilih.' : 'Kosongkan tanggal untuk melihat jadwal hari ini dan seterusnya.' }}
                </p>
            </form>
        </section>

        {{-- Daftar jadwal per tanggal --}}
        @forelse ($jadwal as $tanggal => $items)
            <section class="rounded-[20px] bg-white shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-dash-line/70 px-6 py-4 lg:px-8">
                    <div class="flex items-center gap-3">
                        <h2 class="text-[17px] font-semibold tracking-tight text-dash-ink lg:text-[18px]">
                            {{ $formatTanggal($tanggal) }}
                        </h2>

                        @if ($tanggal === $hariIni)
                            <x-dashboard.badge tone="green">Hari ini</x-dashboard.badge>
                        @endif
                    </div>

                    <p class="text-[13px] text-dash-muted">{{ $items->count() }} jadwal</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-left">
                        <thead>
                            <tr class="border-b border-dash-line/70 bg-dash-surface/60 text-[13px] uppercase tracking-wide text-dash-muted">
                                <th scope="col" class="px-6 py-3.5 font-medium lg:px-8">Jam</th>
                                <th scope="col" class="px-6 py-3.5 font-medium">Ruang</th>
                                <th scope="col" class="px-6 py-3.5 font-medium">Tenant</th>
                                <th scope="col" class="px-6 py-3.5 font-medium">Peserta</th>
                                <th scope="col" class="px-6 py-3.5 font-medium lg:pr-8">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-dash-line/70">
                            @foreach ($items as $booking)
                                <tr class="align-middle transition hover:bg-dash-surface/60">
                                    <td class="whitespace-nowrap px-6 py-4 text-[15px] font-semibold text-dash-ink lg:px-8">
                                        {{ $booking->rentang() }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-[15px] text-dash-ink">{{ $booking->room->nama_ruang }}</p>
                                        <p class="text-[13px] text-dash-muted">{{ $booking->room->kode_ruang }}</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-[15px] font-medium text-dash-ink">{{ $booking->tenant->nama_perusahaan }}</p>
                                        <p class="text-[13px] text-dash-muted">{{ $booking->tenant->nama_pic }}</p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                        {{ $booking->jumlah_peserta }} orang
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 lg:pr-8">
                                        <x-dashboard.badge :tone="$booking->statusTone()">{{ $booking->status }}</x-dashboard.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @empty
            <section class="rounded-[20px] bg-white px-6 py-16 text-center shadow-[0_4px_20px_rgba(25,35,80,0.03)] lg:px-8">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-dash-surface text-dash-muted">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                </span>

                <p class="mt-4 text-[15px] font-medium text-dash-ink">
                    {{ $isFiltered ? 'Tidak ada jadwal yang cocok dengan filter.' : 'Belum ada jadwal penggunaan ruang.' }}
                </p>
                <p class="mt-1 text-[13px] text-dash-muted">
                    {{ $isFiltered ? 'Coba ubah ruang atau tanggal yang dipilih.' : 'Jadwal akan muncul setelah pemesanan ruang disetujui.' }}
                </p>

                <div class="mt-5 flex justify-center">
                    @if ($isFiltered)
                        <x-dashboard.button variant="secondary" :href="route('jadwal-ruang.index')">Reset Filter</x-dashboard.button>
                    @else
                        <x-dashboard.button :href="route('pemesanan.create')">Tambah Pemesanan</x-dashboard.button>
                    @endif
                </div>
            </section>
        @endforelse
    </div>
</x-layouts.dashboard>
