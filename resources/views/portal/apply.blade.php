<x-layouts.dashboard title="Ajukan Sewa" heading="Ajukan Sewa">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Lengkapi data di bawah ini untuk mengajukan sewa kantor virtual. Pengajuan akan
            diverifikasi admin sebelum keanggotaan aktif. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        <form method="POST" action="{{ route('portal.apply.store') }}" class="space-y-6">
            @csrf

            <x-tenants.form-fields
                :tenant="$tenant"
                :show-kode-tenant="false"
                :show-status="false"
                :show-period-request="true"
                detail-note="Perkiraan periode sewa yang diajukan. Tanggal mulai sewa tidak boleh sebelum hari ini."
            />

            <div class="flex flex-wrap items-center justify-end gap-3">
                <x-dashboard.button variant="secondary" :href="route('portal')">Batal</x-dashboard.button>
                <x-dashboard.button type="submit">Kirim Pengajuan</x-dashboard.button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
