<x-layouts.dashboard title="Edit Kontrak Sewa" heading="Edit Kontrak Sewa">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Perbarui kontrak <span class="font-medium text-dash-ink">{{ $contract->nomor_kontrak }}</span>
            milik {{ $contract->tenant?->nama_perusahaan }}. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('contracts.form', [
            'action' => route('kontrak-sewa.update', $contract),
            'method' => 'PUT',
            'submitLabel' => 'Perbarui Kontrak',
        ])
    </div>
</x-layouts.dashboard>
