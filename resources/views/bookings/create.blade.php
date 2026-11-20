<x-layouts.dashboard title="Tambah Pemesanan" heading="Tambah Pemesanan">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Catat pemesanan ruang oleh tenant. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('bookings.form', [
            'action' => route('pemesanan.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Pemesanan',
        ])
    </div>
</x-layouts.dashboard>
