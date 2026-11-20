<x-layouts.dashboard title="Edit Tenant" heading="Edit Data Tenant">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Perbarui data tenant <span class="font-medium text-dash-ink">{{ $tenant->nama_pic }}</span>
            ({{ $tenant->kode_tenant }}). Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('tenants.form', [
            'action' => route('tenants.update', $tenant),
            'method' => 'PUT',
            'submitLabel' => 'Perbarui Data Tenant',
        ])
    </div>
</x-layouts.dashboard>
