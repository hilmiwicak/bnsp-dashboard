@php
    $isFiltered = $filters['q'] !== '' || $filters['tipe'] || $filters['status'];
@endphp

<x-layouts.dashboard title="Data Ruang & Fasilitas" heading="Data Ruang & Fasilitas">
    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success">{{ session('status') }}</x-alert>
        @endif

        <x-dashboard.data-table
            title="Daftar Ruang & Fasilitas"
            :subtitle="$totalSemua.' ruang terdaftar pada kantor virtual.'"
            :paginator="$rooms"
            unit="ruang"
        >
            <x-slot:action>
                <x-dashboard.button :href="route('ruang-fasilitas.create')" class="shrink-0">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Ruang
                </x-dashboard.button>
            </x-slot:action>

            <x-slot:filters>
                <form method="GET" action="{{ route('ruang-fasilitas.index') }}" class="flex flex-wrap items-end gap-3 px-6 py-5 lg:px-8">
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
                                placeholder="Cari kode, nama ruang, atau lokasi"
                                class="h-[50px] w-full rounded-2xl border border-dash-line bg-white pl-11 pr-4 text-[15px] text-dash-ink outline-none transition placeholder:text-dash-soft focus:border-dash-primary focus:ring-4 focus:ring-dash-primary/10"
                            />
                        </div>
                    </div>

                    <div class="w-full sm:w-[190px]">
                        <x-dashboard.form-select
                            name="tipe"
                            label="Tipe Ruang"
                            :options="\App\Models\Room::TIPE"
                            :selected="$filters['tipe']"
                            placeholder="Semua Tipe"
                        />
                    </div>

                    <div class="w-full sm:w-[170px]">
                        <x-dashboard.form-select
                            name="status"
                            label="Status"
                            :options="\App\Models\Room::STATUS"
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
                            <x-dashboard.button variant="secondary" :href="route('ruang-fasilitas.index')">Reset</x-dashboard.button>
                        @endif
                    </div>
                </form>
            </x-slot:filters>

            <table class="w-full min-w-[980px] text-left">
                <thead>
                    <tr class="border-y border-dash-line/70 bg-dash-surface/60 text-[13px] uppercase tracking-wide text-dash-muted">
                        <th scope="col" class="px-6 py-4 font-medium lg:px-8">Kode</th>
                        <th scope="col" class="px-6 py-4 font-medium">Ruang</th>
                        <th scope="col" class="px-6 py-4 font-medium">Kapasitas</th>
                        <th scope="col" class="px-6 py-4 font-medium">Lokasi</th>
                        <th scope="col" class="px-6 py-4 font-medium">Tarif / Jam</th>
                        <th scope="col" class="px-6 py-4 font-medium">Status</th>
                        <th scope="col" class="px-6 py-4 text-right font-medium lg:px-8">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-dash-line/70">
                    @forelse ($rooms as $room)
                        <tr class="align-middle transition hover:bg-dash-surface/60">
                            <td class="whitespace-nowrap px-6 py-4 text-[15px] font-semibold text-dash-ink lg:px-8">
                                {{ $room->kode_ruang }}
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-[15px] font-medium text-dash-ink">{{ $room->nama_ruang }}</p>
                                <p class="text-[13px] text-dash-muted">{{ $room->tipe }}</p>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                {{ $room->kapasitas }} orang
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                {{ $room->lokasi }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                {{ $room->hargaPerJam() }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <x-dashboard.badge :tone="$room->statusTone()">{{ $room->status }}</x-dashboard.badge>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right lg:px-8">
                                <div class="inline-flex items-center gap-2">
                                    <x-dashboard.table-action :href="route('ruang-fasilitas.edit', $room)">Edit</x-dashboard.table-action>

                                    <x-dashboard.table-action
                                        :href="route('ruang-fasilitas.destroy', $room)"
                                        method="DELETE"
                                        tone="danger"
                                        confirm="Hapus data ruang ini?"
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
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                    </svg>
                                </span>

                                <p class="mt-4 text-[15px] font-medium text-dash-ink">
                                    {{ $isFiltered ? 'Tidak ada ruang yang cocok dengan pencarian.' : 'Belum ada data ruang.' }}
                                </p>
                                <p class="mt-1 text-[13px] text-dash-muted">
                                    {{ $isFiltered ? 'Coba ubah kata kunci atau filter yang digunakan.' : 'Tambahkan ruang pertama untuk mulai mengelola fasilitas kantor virtual.' }}
                                </p>

                                <div class="mt-5 flex justify-center">
                                    @if ($isFiltered)
                                        <x-dashboard.button variant="secondary" :href="route('ruang-fasilitas.index')">Reset Filter</x-dashboard.button>
                                    @else
                                        <x-dashboard.button :href="route('ruang-fasilitas.create')">Tambah Ruang</x-dashboard.button>
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
