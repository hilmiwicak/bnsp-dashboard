@php
    $card = 'rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)] lg:p-8';
    $legend = 'text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]';
    $note = 'mt-1 text-[13px] text-dash-muted';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (! in_array(strtoupper($method), ['GET', 'POST'], true))
        @method($method)
    @endif

    <section class="{{ $card }}">
        <div class="mb-6">
            <h3 class="{{ $legend }}">Pemesan & Ruang</h3>
            <p class="{{ $note }}">Tenant yang mengajukan pemesanan dan ruang yang akan dipakai.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-field
                name="kode_pemesanan"
                label="Kode Pemesanan"
                placeholder="PMN-001"
                :value="$booking->kode_pemesanan"
                :error="$errors->first('kode_pemesanan')"
                required
            />

            <x-dashboard.form-select
                name="tenant_id"
                label="Tenant"
                :options="$tenants"
                :selected="$booking->tenant_id"
                placeholder="Pilih tenant"
                :error="$errors->first('tenant_id')"
                required
            />

            <div class="md:col-span-2">
                <x-dashboard.form-select
                    name="room_id"
                    label="Ruang"
                    :options="$rooms"
                    :selected="$booking->room_id"
                    placeholder="Pilih ruang"
                    :error="$errors->first('room_id')"
                    required
                />
            </div>
        </div>
    </section>

    <section class="{{ $card }}">
        <div class="mb-6">
            <h3 class="{{ $legend }}">Jadwal Pemakaian</h3>
            <p class="{{ $note }}">Sistem menolak jadwal yang bertabrakan dengan pemesanan lain pada ruang yang sama.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-field
                name="tanggal"
                label="Tanggal"
                type="date"
                :value="$booking->tanggal?->format('Y-m-d')"
                :error="$errors->first('tanggal')"
                required
            />

            <x-dashboard.form-field
                name="jumlah_peserta"
                label="Jumlah Peserta"
                type="number"
                placeholder="4"
                :value="$booking->jumlah_peserta"
                :error="$errors->first('jumlah_peserta')"
                required
            />

            <x-dashboard.form-field
                name="jam_mulai"
                label="Jam Mulai"
                type="time"
                :value="$booking->jamMulaiShort()"
                :error="$errors->first('jam_mulai')"
                required
            />

            <x-dashboard.form-field
                name="jam_selesai"
                label="Jam Selesai"
                type="time"
                :value="$booking->jamSelesaiShort()"
                :error="$errors->first('jam_selesai')"
                required
            />
        </div>
    </section>

    <section class="{{ $card }}">
        <div class="mb-6">
            <h3 class="{{ $legend }}">Status & Catatan</h3>
            <p class="{{ $note }}">Status pemesanan dan keterangan tambahan dari tenant.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-select
                name="status"
                label="Status Pemesanan"
                :options="\App\Models\Booking::STATUS"
                :selected="$booking->status"
                placeholder="Pilih status"
                :error="$errors->first('status')"
                required
            />

            <div class="md:col-span-2">
                <x-dashboard.form-field
                    name="catatan"
                    label="Catatan"
                    type="textarea"
                    placeholder="Keperluan rapat atau permintaan khusus"
                    :value="$booking->catatan"
                    :error="$errors->first('catatan')"
                    hint="Boleh dikosongkan."
                />
            </div>
        </div>
    </section>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <x-dashboard.button variant="secondary" :href="route('pemesanan.index')">Batal</x-dashboard.button>
        <x-dashboard.button type="submit">{{ $submitLabel }}</x-dashboard.button>
    </div>
</form>
