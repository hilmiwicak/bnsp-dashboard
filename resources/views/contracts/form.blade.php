@php
    $card = 'rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)] lg:p-8';
    $legend = 'text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]';
    $note = 'mt-1 text-[13px] text-dash-muted';
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (! in_array(strtoupper($method), ['GET', 'POST'], true))
        @method($method)
    @endif

    <section class="{{ $card }}">
        <div class="mb-6">
            <h3 class="{{ $legend }}">Informasi Kontrak</h3>
            <p class="{{ $note }}">Nomor kontrak dan tenant yang menandatangani perjanjian sewa.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-field
                name="nomor_kontrak"
                label="Nomor Kontrak"
                placeholder="KTR-001"
                :value="$contract->nomor_kontrak"
                :error="$errors->first('nomor_kontrak')"
                required
            />

            <x-dashboard.form-select
                name="tenant_id"
                label="Tenant"
                :options="$tenants"
                :selected="$contract->tenant_id"
                placeholder="Pilih tenant"
                :error="$errors->first('tenant_id')"
                required
            />
        </div>
    </section>

    <section class="{{ $card }}">
        <div class="mb-6">
            <h3 class="{{ $legend }}">Periode & Nilai Sewa</h3>
            <p class="{{ $note }}">Masa berlaku kontrak dan nilai sewa yang disepakati.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-field
                name="tanggal_mulai"
                label="Tanggal Mulai"
                type="date"
                :value="$contract->tanggal_mulai?->format('Y-m-d')"
                :error="$errors->first('tanggal_mulai')"
                required
            />

            <x-dashboard.form-field
                name="tanggal_berakhir"
                label="Tanggal Berakhir"
                type="date"
                :value="$contract->tanggal_berakhir?->format('Y-m-d')"
                :error="$errors->first('tanggal_berakhir')"
                hint="Harus sama atau setelah tanggal mulai."
                required
            />

            <div class="md:col-span-2">
                <x-dashboard.form-field
                    name="nilai_sewa"
                    label="Nilai Sewa"
                    type="number"
                    placeholder="12000000"
                    :value="$contract->nilai_sewa"
                    :error="$errors->first('nilai_sewa')"
                    hint="Diisi dalam rupiah, tanpa titik atau koma."
                    required
                />
            </div>
        </div>
    </section>

    <section class="{{ $card }}">
        <div class="mb-6">
            <h3 class="{{ $legend }}">Status & Catatan</h3>
            <p class="{{ $note }}">Status kontrak dan keterangan tambahan bila diperlukan.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-select
                name="status"
                label="Status Kontrak"
                :options="\App\Models\Contract::STATUS"
                :selected="$contract->status"
                placeholder="Pilih status"
                :error="$errors->first('status')"
                required
            />

            <div class="md:col-span-2">
                <x-dashboard.form-field
                    name="catatan"
                    label="Catatan"
                    type="textarea"
                    placeholder="Ketentuan khusus atau alasan pembatalan"
                    :value="$contract->catatan"
                    :error="$errors->first('catatan')"
                    hint="Boleh dikosongkan."
                />
            </div>
        </div>
    </section>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <x-dashboard.button variant="secondary" :href="route('kontrak-sewa.index')">Batal</x-dashboard.button>
        <x-dashboard.button type="submit">{{ $submitLabel }}</x-dashboard.button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tenantSelect = document.getElementById('tenant_id');
        const startInput = document.getElementById('tanggal_mulai');
        const tenantStarts = @json($tenantStarts ?? []);

        if (! tenantSelect || ! startInput) {
            return;
        }

        tenantSelect.addEventListener('change', function () {
            const startDate = tenantStarts[tenantSelect.value];

            if (startDate) {
                startInput.value = startDate;
            }
        });
    });
</script>
