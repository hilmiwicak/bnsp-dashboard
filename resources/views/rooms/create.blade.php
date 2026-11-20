<x-layouts.dashboard title="Tambah Ruang" heading="Tambah Ruang">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Lengkapi data ruang dan fasilitas kantor virtual di bawah ini. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('rooms.form', [
            'action' => route('ruang-fasilitas.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Data Ruang',
        ])
    </div>
</x-layouts.dashboard>
