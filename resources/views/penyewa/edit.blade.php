<x-layouts.dashboard title="Edit Penyewa" heading="Edit Data Penyewa">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Perbarui data penyewa <span class="font-medium text-dash-ink">{{ $penyewa->nama_pic }}</span>
            ({{ $penyewa->kode_penyewa }}). Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('penyewa.form', [
            'action' => route('penyewa.update', $penyewa),
            'method' => 'PUT',
            'submitLabel' => 'Perbarui Data Penyewa',
        ])
    </div>
</x-layouts.dashboard>
