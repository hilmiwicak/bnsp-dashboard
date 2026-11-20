<x-layouts.dashboard title="Terbitkan Tagihan" heading="Terbitkan Tagihan">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Lengkapi data tagihan tenant di bawah ini. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('invoices.form', [
            'action' => route('tagihan-pembayaran.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Tagihan',
        ])
    </div>
</x-layouts.dashboard>
