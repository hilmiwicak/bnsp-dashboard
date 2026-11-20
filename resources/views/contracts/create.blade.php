<x-layouts.dashboard title="Tambah Kontrak Sewa" heading="Tambah Kontrak Sewa">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Lengkapi data kontrak sewa tenant di bawah ini. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('contracts.form', [
            'action' => route('kontrak-sewa.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Kontrak',
        ])
    </div>
</x-layouts.dashboard>
