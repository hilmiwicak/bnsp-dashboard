<x-layouts.dashboard title="Tambah Tenant" heading="Tambah Tenant">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Lengkapi data tenant kantor virtual di bawah ini. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('tenants.form', [
            'action' => route('tenants.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Data Tenant',
        ])
    </div>
</x-layouts.dashboard>
