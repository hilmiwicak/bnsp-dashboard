@props([
    'tenant',
    'showKodeTenant' => true,
    'showStatus' => true,
    'showPeriodRequest' => false,
    'detailNote' => 'Status keanggotaan tenant.',
])

@php
    $card = 'rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)] lg:p-8';
    $legend = 'text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]';
    $note = 'mt-1 text-[13px] text-dash-muted';
@endphp

<section class="{{ $card }}">
    <div class="mb-6">
        <h3 class="{{ $legend }}">Informasi PIC</h3>
        <p class="{{ $note }}">Data penanggung jawab yang menjadi kontak utama tenant.</p>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        @if ($showKodeTenant)
            <x-dashboard.form-field
                name="kode_tenant"
                label="Kode Tenant"
                placeholder="TNT-001"
                :value="$tenant->kode_tenant"
                :error="$errors->first('kode_tenant')"
                required
            />
        @endif

        <x-dashboard.form-field
            name="nama_pic"
            label="Nama Lengkap PIC"
            placeholder="Nama lengkap penanggung jawab"
            :value="$tenant->nama_pic"
            :error="$errors->first('nama_pic')"
            required
        />

        <x-dashboard.form-field
            name="no_hp"
            label="Nomor HP"
            type="tel"
            placeholder="08xxxxxxxxxx"
            :value="$tenant->no_hp"
            :error="$errors->first('no_hp')"
            required
        />

        <x-dashboard.form-field
            name="email"
            label="Email"
            type="email"
            placeholder="nama@perusahaan.com"
            :value="$tenant->email"
            :error="$errors->first('email')"
            required
        />
    </div>
</section>

<section class="{{ $card }}">
    <div class="mb-6">
        <h3 class="{{ $legend }}">Informasi Perusahaan</h3>
        <p class="{{ $note }}">Identitas badan usaha atau perorangan yang menyewa.</p>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <x-dashboard.form-field
            name="nama_perusahaan"
            label="Nama Perusahaan"
            placeholder="PT Contoh Sejahtera"
            :value="$tenant->nama_perusahaan"
            :error="$errors->first('nama_perusahaan')"
            required
        />

        <x-dashboard.form-field
            name="nib_npwp"
            label="NIB / NPWP"
            placeholder="Opsional"
            :value="$tenant->nib_npwp"
            :error="$errors->first('nib_npwp')"
            hint="Boleh dikosongkan bila belum tersedia."
        />

        <div class="md:col-span-2">
            <x-dashboard.form-field
                name="alamat"
                label="Alamat Perusahaan"
                type="textarea"
                placeholder="Jalan, nomor, kelurahan, kota, kode pos"
                :value="$tenant->alamat"
                :error="$errors->first('alamat')"
                required
            />
        </div>
    </div>
</section>

<section class="{{ $card }}">
    <div class="mb-6">
        <h3 class="{{ $legend }}">{{ $showPeriodRequest ? 'Detail Sewa' : 'Status Keanggotaan' }}</h3>
        <p class="{{ $note }}">{{ $detailNote }}</p>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        @if ($showStatus)
            <x-dashboard.form-select
                name="status"
                label="Status Tenant"
                :options="\App\Models\Tenant::STATUS"
                :selected="$tenant->status"
                placeholder="Pilih status"
                :error="$errors->first('status')"
                required
            />
        @endif

        @if ($showPeriodRequest)
            <x-dashboard.form-field
                name="tanggal_mulai_sewa"
                label="Tanggal Mulai Sewa"
                type="date"
                :value="now()->format('Y-m-d')"
                :error="$errors->first('tanggal_mulai_sewa')"
                required
            />

            <x-dashboard.form-field
                name="durasi_sewa"
                label="Durasi Sewa"
                type="number"
                placeholder="12"
                :value="12"
                :error="$errors->first('durasi_sewa')"
                hint="Diisi dalam satuan bulan, minimal 1 dan maksimal 120."
                required
            />
        @endif
    </div>
</section>
