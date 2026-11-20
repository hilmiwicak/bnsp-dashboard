@php
    $rupiah = fn (int $nilai) => 'Rp'.number_format($nilai, 0, ',', '.');
@endphp

<x-layouts.dashboard title="Laporan" heading="Laporan">
    <div class="space-y-6">
        {{-- Filter periode --}}
        <section class="rounded-[20px] bg-white shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
            <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-end justify-between gap-4 px-6 py-5 lg:px-8">
                <div>
                    <h2 class="text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">Laporan Tahunan</h2>
                    <p class="mt-1 max-w-[520px] text-[13px] text-dash-muted">
                        Rekap pemesanan ruang, pendapatan, dan tagihan kantor virtual per bulan.
                    </p>
                </div>

                <div class="flex items-end gap-3">
                    <div class="w-[150px]">
                        <x-dashboard.form-select
                            name="tahun"
                            label="Tahun"
                            :options="$tahunTersedia"
                            :selected="$tahun"
                        />
                    </div>

                    <x-dashboard.button type="submit">Tampilkan</x-dashboard.button>
                </div>
            </form>
        </section>

        {{-- Ringkasan setahun --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-dashboard.stat-card
                label="Total Pemesanan"
                :value="(string) $totalPemesanan"
                :hint="'Sepanjang tahun '.$tahun"
                icon="calendar"
                tone="teal"
            />

            <x-dashboard.stat-card
                label="Pendapatan Lunas"
                :value="$rupiah($totalPendapatan)"
                :hint="'Pembayaran diterima tahun '.$tahun"
                icon="banknotes"
                tone="indigo"
            />

            <x-dashboard.stat-card
                label="Tagihan Belum Lunas"
                :value="$rupiah($totalBelumLunas)"
                :hint="'Periode tagihan '.$tahun"
                icon="clock"
                tone="amber"
            />
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Rekap per bulan --}}
            <section class="lg:col-span-8">
                <x-dashboard.data-table
                    :title="'Rekap Bulanan '.$tahun"
                    subtitle="Pendapatan dihitung dari tagihan berstatus lunas pada bulan pembayaran."
                >
                    <table class="w-full min-w-[720px] text-left">
                        <thead>
                            <tr class="border-y border-dash-line/70 bg-dash-surface/60 text-[13px] uppercase tracking-wide text-dash-muted">
                                <th scope="col" class="px-6 py-4 font-medium lg:px-8">Bulan</th>
                                <th scope="col" class="px-6 py-4 font-medium">Pemesanan</th>
                                <th scope="col" class="px-6 py-4 font-medium">Pendapatan Lunas</th>
                                <th scope="col" class="px-6 py-4 font-medium lg:pr-8">Tagihan Belum Lunas</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-dash-line/70">
                            @foreach ($baris as $row)
                                <tr class="align-middle transition hover:bg-dash-surface/60">
                                    <td class="whitespace-nowrap px-6 py-3.5 text-[15px] font-medium text-dash-ink lg:px-8">
                                        {{ $row['bulan'] }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-3.5 text-[15px] text-dash-ink">
                                        {{ $row['pemesanan'] }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-3.5 text-[15px] {{ $row['pendapatan'] > 0 ? 'text-dash-ink' : 'text-dash-soft' }}">
                                        {{ $rupiah($row['pendapatan']) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-3.5 text-[15px] lg:pr-8 {{ $row['belum_lunas'] > 0 ? 'text-dash-ink' : 'text-dash-soft' }}">
                                        {{ $rupiah($row['belum_lunas']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr class="border-t border-dash-line bg-dash-surface/60 text-[15px] font-semibold text-dash-ink">
                                <td class="px-6 py-4 lg:px-8">Total</td>
                                <td class="px-6 py-4">{{ $totalPemesanan }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $rupiah($totalPendapatan) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 lg:pr-8">{{ $rupiah($totalBelumLunas) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </x-dashboard.data-table>
            </section>

            {{-- Komposisi status pemesanan --}}
            <section class="lg:col-span-4">
                <x-dashboard.section-title :title="'Status Pemesanan '.$tahun" />

                <div class="rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                    <ul class="space-y-4">
                        @foreach ($statusPemesanan as $item)
                            <li class="flex items-center justify-between gap-3">
                                <x-dashboard.badge :tone="$item['tone']">{{ $item['status'] }}</x-dashboard.badge>
                                <span class="text-[15px] font-semibold text-dash-ink">{{ $item['jumlah'] }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($totalPemesanan === 0)
                        <p class="mt-5 text-[13px] text-dash-muted">
                            Belum ada pemesanan ruang pada tahun {{ $tahun }}.
                        </p>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-layouts.dashboard>
