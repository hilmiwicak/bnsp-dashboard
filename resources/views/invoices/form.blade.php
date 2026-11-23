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
            <h3 class="{{ $legend }}">Informasi Tagihan</h3>
            <p class="{{ $note }}">Nomor tagihan, tenant yang ditagih, dan periode penagihan.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-field
                name="nomor_tagihan"
                label="Nomor Tagihan"
                placeholder="INV-001"
                :value="$invoice->nomor_tagihan"
                :error="$errors->first('nomor_tagihan')"
                required
            />

            <x-dashboard.form-select
                name="tenant_id"
                label="Tenant"
                :options="$tenants"
                :selected="$invoice->tenant_id"
                placeholder="Pilih tenant"
                :error="$errors->first('tenant_id')"
                required
            />

            <x-dashboard.form-field
                name="periode"
                label="Periode"
                type="month"
                :value="$invoice->periode"
                :error="$errors->first('periode')"
                hint="Satu tagihan hanya untuk satu bulan. Samakan dengan bulan tanggal pemesanan, format YYYY-MM, mis. 2026-11."
                required
            />

            <x-dashboard.form-field
                name="jumlah"
                label="Jumlah Tagihan"
                type="number"
                placeholder="2500000"
                :value="$invoice->jumlah"
                :error="$errors->first('jumlah')"
                hint="Diisi dalam rupiah, tanpa titik atau koma."
                required
            />
        </div>
    </section>

    <section class="{{ $card }}">
        <div class="mb-6">
            <h3 class="{{ $legend }}">Pembayaran</h3>
            <p class="{{ $note }}">Jatuh tempo, tanggal pembayaran, dan status tagihan.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-dashboard.form-field
                name="jatuh_tempo"
                label="Jatuh Tempo"
                type="date"
                :value="$invoice->jatuh_tempo?->format('Y-m-d')"
                :error="$errors->first('jatuh_tempo')"
                required
            />

            <x-dashboard.form-field
                name="tanggal_bayar"
                label="Tanggal Bayar"
                type="date"
                :value="$invoice->tanggal_bayar?->format('Y-m-d')"
                :error="$errors->first('tanggal_bayar')"
                hint="Kosongkan bila tagihan belum dibayar."
            />

            <x-dashboard.form-select
                name="status"
                label="Status Tagihan"
                :options="\App\Models\Invoice::STATUS"
                :selected="$invoice->status"
                placeholder="Pilih status"
                :error="$errors->first('status')"
                required
            />

            <div class="md:col-span-2">
                <x-dashboard.form-field
                    name="catatan"
                    label="Catatan"
                    type="textarea"
                    placeholder="Alasan nominal tagihan, mis. 3 pemesanan disetujui bulan November"
                    :value="$invoice->catatan"
                    :error="$errors->first('catatan')"
                    hint="Boleh dikosongkan. Berguna untuk menjelaskan dasar nominal."
                />
            </div>
        </div>
    </section>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <x-dashboard.button variant="secondary" :href="route('tagihan-pembayaran.index')">Batal</x-dashboard.button>
        <x-dashboard.button type="submit">{{ $submitLabel }}</x-dashboard.button>
    </div>
</form>
