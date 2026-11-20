<x-layouts.dashboard title="Edit Pemesanan" heading="Edit Pemesanan">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Perbarui pemesanan <span class="font-medium text-dash-ink">{{ $booking->kode_pemesanan }}</span>.
            Kolom bertanda <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('bookings.form', [
            'action' => route('pemesanan.update', $booking),
            'method' => 'PUT',
            'submitLabel' => 'Perbarui Pemesanan',
        ])
    </div>
</x-layouts.dashboard>
