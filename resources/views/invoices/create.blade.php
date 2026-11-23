@php
    $card = 'rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)] lg:p-8';
    $legend = 'text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]';
    $note = 'mt-1 text-[13px] text-dash-muted';
@endphp

<x-layouts.dashboard title="Terbitkan Tagihan" heading="Terbitkan Tagihan">
    <div class="mx-auto max-w-[980px] space-y-6">
        <p class="text-[15px] text-dash-muted">
            Lengkapi data tagihan tenant di bawah ini. Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        <section class="{{ $card }}">
            <div class="mb-6">
                <h3 class="{{ $legend }}">Hitung dari Pemakaian Ruang</h3>
                <p class="{{ $note }}">
                    Pilih tenant dan periode, lalu sistem menghitung ulang tarif pemesanan
                    yang sudah disetujui pada bulan tersebut.
                </p>
            </div>

            <form
                method="GET"
                action="{{ route('tagihan-pembayaran.create') }}"
                class="grid grid-cols-1 items-end gap-4 sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]"
            >
                <x-dashboard.form-select
                    name="tenant_id"
                    label="Tenant"
                    :options="$tenants"
                    :selected="$filter['tenant_id']"
                    placeholder="Pilih tenant"
                />

                <x-dashboard.form-field
                    name="periode"
                    label="Periode"
                    type="month"
                    :value="$filter['periode']"
                />

                <x-dashboard.button type="submit" class="sm:mb-0">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h9.75M8.25 6a3 3 0 0 1-3-3m12 0a3 3 0 0 1-3 3M5.25 6v12a2.25 2.25 0 0 0 2.25 2.25h9A2.25 2.25 0 0 0 18.75 18V6" />
                    </svg>
                    Hitung
                </x-dashboard.button>
            </form>

            @if ($saran)
                <div class="mt-6 rounded-2xl border border-[#d9cffb] bg-[#f4f0ff] p-5">
                    @if ($saran['pemesanan'] === 0)
                        <p class="text-[14px] text-[#2e1465]">
                            Tidak ada pemesanan berstatus <span class="font-medium">Disetujui</span> atau
                            <span class="font-medium">Selesai</span> pada periode ini.
                            Jumlah tagihan diisi manual.
                        </p>
                    @else
                        <p class="text-[14px] text-[#2e1465]">
                            <span class="font-semibold">{{ $saran['pemesanan'] }} pemesanan</span>
                            &middot; {{ \App\Support\TagihanRuang::jam($saran['menit']) }}
                            &middot; total
                            <span class="font-semibold">Rp{{ number_format($saran['total'], 0, ',', '.') }}</span>
                        </p>
                        <p class="mt-1 text-[13px] text-[#5d28bd]">
                            Jumlah tagihan di bawah sudah terisi otomatis. Ubah bila perlu, lalu
                            tuliskan alasannya pada kolom catatan.
                        </p>
                    @endif
                </div>
            @endif

            @if ($duplikat > 0)
                <div class="mt-4">
                    <x-alert type="error">
                        Sudah ada {{ $duplikat }} tagihan untuk tenant dan periode ini.
                        Periksa daftar tagihan sebelum menerbitkan yang baru.
                    </x-alert>
                </div>
            @endif
        </section>

        @include('invoices.form', [
            'action' => route('tagihan-pembayaran.store'),
            'method' => 'POST',
            'submitLabel' => 'Simpan Tagihan',
        ])
    </div>
</x-layouts.dashboard>
