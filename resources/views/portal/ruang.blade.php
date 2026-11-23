@php
    $card = 'rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)] lg:p-8';
    $legend = 'text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]';
    $note = 'mt-1 text-[13px] text-dash-muted';

    $roomOptions = $rooms->mapWithKeys(fn ($room) => [
        $room->id => "{$room->kode_ruang} — {$room->nama_ruang}",
    ]);
@endphp

<x-layouts.dashboard title="Ruang & Fasilitas" heading="Ruang & Fasilitas">
    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success">{{ session('status') }}</x-alert>
        @endif

        <section class="{{ $card }}">
            <div class="mb-6">
                <h2 class="{{ $legend }}">Ruang Tersedia</h2>
                <p class="{{ $note }}">
                    Ruang berikut sedang tersedia untuk dipesan. Ajukan jadwal pemakaian,
                    lalu tunggu persetujuan admin kantor virtual.
                </p>
            </div>

            @if ($rooms->isEmpty())
                <div class="rounded-2xl border border-dashed border-dash-line bg-dash-surface/60 px-6 py-12 text-center">
                    <p class="text-[15px] font-medium text-dash-ink">Belum ada ruang yang tersedia.</p>
                    <p class="mt-1 text-[13px] text-dash-muted">Silakan hubungi admin kantor virtual untuk informasi lebih lanjut.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($rooms as $room)
                        <article class="rounded-2xl border border-dash-line/70 bg-dash-surface/40 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-medium text-dash-muted">{{ $room->kode_ruang }}</p>
                                    <h3 class="truncate text-[16px] font-semibold text-dash-ink">{{ $room->nama_ruang }}</h3>
                                </div>

                                <x-dashboard.badge tone="green">{{ $room->status }}</x-dashboard.badge>
                            </div>

                            <dl class="mt-4 space-y-2 text-[13px]">
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-dash-muted">Tipe</dt>
                                    <dd class="font-medium text-dash-ink">{{ $room->tipe }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-dash-muted">Kapasitas</dt>
                                    <dd class="font-medium text-dash-ink">{{ $room->kapasitas }} orang</dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-dash-muted">Lokasi</dt>
                                    <dd class="text-right font-medium text-dash-ink">{{ $room->lokasi }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-dash-muted">Harga</dt>
                                    <dd class="font-medium text-dash-ink">{{ $room->hargaPerJam() }} / jam</dd>
                                </div>
                            </dl>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($rooms->isNotEmpty())
            <section class="{{ $card }}">
                <div class="mb-6">
                    <h2 class="{{ $legend }}">Ajukan Pemesanan</h2>
                    <p class="{{ $note }}">
                        Isi jadwal yang Anda butuhkan. Permintaan dikirim dengan status
                        <span class="font-medium text-dash-ink">Menunggu</span> sampai disetujui admin.
                    </p>
                </div>

                <form method="POST" action="{{ route('portal.ruang.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-dashboard.form-select
                                name="room_id"
                                label="Ruang"
                                :options="$roomOptions"
                                placeholder="Pilih ruang"
                                :error="$errors->first('room_id')"
                                required
                            />
                        </div>

                        <x-dashboard.form-field
                            name="tanggal"
                            label="Tanggal"
                            type="date"
                            :value="now()->toDateString()"
                            :error="$errors->first('tanggal')"
                            required
                        />

                        <x-dashboard.form-field
                            name="jumlah_peserta"
                            label="Jumlah Peserta"
                            type="number"
                            value="4"
                            :error="$errors->first('jumlah_peserta')"
                            required
                        />

                        <x-dashboard.form-field
                            name="jam_mulai"
                            label="Jam Mulai"
                            type="time"
                            value="09:00"
                            :error="$errors->first('jam_mulai')"
                            required
                        />

                        <x-dashboard.form-field
                            name="jam_selesai"
                            label="Jam Selesai"
                            type="time"
                            value="10:00"
                            :error="$errors->first('jam_selesai')"
                            required
                        />

                        <div class="md:col-span-2">
                            <x-dashboard.form-field
                                name="catatan"
                                label="Catatan"
                                type="textarea"
                                placeholder="Kebutuhan tambahan, mis. proyektor atau kursi ekstra"
                                :error="$errors->first('catatan')"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-dashboard.button type="submit">Kirim Permintaan</x-dashboard.button>
                    </div>
                </form>
            </section>
        @endif

        <section class="{{ $card }}">
            <div class="mb-6">
                <h2 class="{{ $legend }}">Permintaan Saya</h2>
                <p class="{{ $note }}">Sepuluh permintaan pemesanan terakhir untuk tenancy Anda.</p>
            </div>

            @if ($bookings->isEmpty())
                <div class="rounded-2xl border border-dashed border-dash-line bg-dash-surface/60 px-6 py-12 text-center">
                    <p class="text-[15px] font-medium text-dash-ink">Belum ada permintaan pemesanan.</p>
                    <p class="mt-1 text-[13px] text-dash-muted">Permintaan yang Anda kirim akan tampil di sini beserta statusnya.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-left">
                        <thead>
                            <tr class="border-y border-dash-line/70 bg-dash-surface/60 text-[13px] uppercase tracking-wide text-dash-muted">
                                <th scope="col" class="px-4 py-3 font-medium">Kode</th>
                                <th scope="col" class="px-4 py-3 font-medium">Ruang</th>
                                <th scope="col" class="px-4 py-3 font-medium">Jadwal</th>
                                <th scope="col" class="px-4 py-3 font-medium">Peserta</th>
                                <th scope="col" class="px-4 py-3 font-medium">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-dash-line/70">
                            @foreach ($bookings as $booking)
                                <tr class="align-middle">
                                    <td class="whitespace-nowrap px-4 py-4 text-[14px] font-semibold text-dash-ink">
                                        {{ $booking->kode_pemesanan }}
                                    </td>
                                    <td class="px-4 py-4 text-[14px] text-dash-ink">
                                        {{ $booking->room?->nama_ruang ?? '—' }}
                                        <span class="block text-[13px] text-dash-muted">{{ $booking->room?->kode_ruang }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-[14px] text-dash-ink">
                                        {{ $booking->tanggal?->format('d/m/Y') }}
                                        <span class="block text-[13px] text-dash-muted">{{ $booking->rentang() }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-[14px] text-dash-ink">
                                        {{ $booking->jumlah_peserta }} orang
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <x-dashboard.badge :tone="$booking->statusTone()">{{ $booking->status }}</x-dashboard.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts.dashboard>
