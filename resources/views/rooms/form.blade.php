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
            <h3 class="{{ $legend }}">Informasi Ruang</h3>
            <p class="{{ $note }}">Identitas dan tarif dasar ruang yang dapat dipesan tenant.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-field
                name="kode_ruang"
                label="Kode Ruang"
                placeholder="RNG-001"
                :value="$room->kode_ruang"
                :error="$errors->first('kode_ruang')"
                required
            />

            <x-dashboard.form-field
                name="nama_ruang"
                label="Nama Ruang"
                placeholder="Anggrek 1"
                :value="$room->nama_ruang"
                :error="$errors->first('nama_ruang')"
                required
            />

            <x-dashboard.form-select
                name="tipe"
                label="Tipe Ruang"
                :options="\App\Models\Room::TIPE"
                :selected="$room->tipe"
                placeholder="Pilih tipe ruang"
                :error="$errors->first('tipe')"
                required
            />

            <x-dashboard.form-field
                name="kapasitas"
                label="Kapasitas"
                type="number"
                placeholder="12"
                :value="$room->kapasitas"
                :error="$errors->first('kapasitas')"
                hint="Jumlah orang yang dapat ditampung."
                required
            />

            <x-dashboard.form-field
                name="lokasi"
                label="Lokasi"
                placeholder="Lantai 3"
                :value="$room->lokasi"
                :error="$errors->first('lokasi')"
                required
            />

            <x-dashboard.form-field
                name="harga_per_jam"
                label="Tarif per Jam"
                type="number"
                placeholder="150000"
                :value="$room->harga_per_jam"
                :error="$errors->first('harga_per_jam')"
                hint="Diisi dalam rupiah, tanpa titik atau koma."
                required
            />

            <x-dashboard.form-select
                name="status"
                label="Status Ruang"
                :options="\App\Models\Room::STATUS"
                :selected="$room->status"
                placeholder="Pilih status"
                :error="$errors->first('status')"
                required
            />
        </div>
    </section>

    <section class="{{ $card }}">
        <div class="mb-6">
            <h3 class="{{ $legend }}">Fasilitas</h3>
            <p class="{{ $note }}">Fasilitas pendukung yang tersedia di ruang ini.</p>
        </div>

        <x-dashboard.form-field
            name="fasilitas"
            label="Daftar Fasilitas"
            type="textarea"
            placeholder="AC, Wi-Fi, Proyektor, Whiteboard"
            :value="$room->fasilitas"
            :error="$errors->first('fasilitas')"
            hint="Pisahkan antar fasilitas dengan koma."
        />
    </section>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <x-dashboard.button variant="secondary" :href="route('ruang-fasilitas.index')">Batal</x-dashboard.button>
        <x-dashboard.button type="submit">{{ $submitLabel }}</x-dashboard.button>
    </div>
</form>
