<x-layouts.dashboard title="Tambah Penyewa" heading="Tambah Penyewa">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Lengkapi data penyewa kantor virtual di bawah ini. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('penyewa.form', [
            'action' => route('penyewa.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Data Penyewa',
        ])
    </div>
</x-layouts.dashboard>
