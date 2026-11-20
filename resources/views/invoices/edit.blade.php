<x-layouts.dashboard title="Edit Tagihan" heading="Edit Tagihan">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Perbarui tagihan <span class="font-medium text-dash-ink">{{ $invoice->nomor_tagihan }}</span>
            milik {{ $invoice->tenant?->nama_perusahaan }}. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('invoices.form', [
            'action' => route('tagihan-pembayaran.update', $invoice),
            'method' => 'PUT',
            'submitLabel' => 'Perbarui Tagihan',
        ])
    </div>
</x-layouts.dashboard>
