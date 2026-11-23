@php
    $isFiltered = $filters['q'] !== '' || $filters['status'] || $filters['tenant'];
    $belumLunas = 'Rp'.number_format($totalBelumLunas, 0, ',', '.');
@endphp

<x-layouts.dashboard title="Tagihan & Pembayaran" heading="Tagihan & Pembayaran">
    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success">{{ session('status') }}</x-alert>
        @endif

        <x-dashboard.data-table
            title="Daftar Tagihan"
            :subtitle="$totalSemua.' tagihan tercatat, '.$belumLunas.' belum lunas.'"
            :paginator="$invoices"
            unit="tagihan"
        >
            <x-slot:action>
                <x-dashboard.button :href="route('tagihan-pembayaran.create')" class="shrink-0">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Terbitkan Tagihan
                </x-dashboard.button>
            </x-slot:action>

            <x-slot:filters>
                <form method="GET" action="{{ route('tagihan-pembayaran.index') }}" class="flex flex-wrap items-end gap-3 px-6 py-5 lg:px-8">
                    <div class="min-w-[220px] flex-1">
                        <label for="q" class="mb-2 block text-[15px] font-medium text-dash-ink">Pencarian</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-dash-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="11" cy="11" r="7.25" />
                                <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                            </svg>
                            <input
                                id="q"
                                name="q"
                                type="search"
                                value="{{ $filters['q'] }}"
                                placeholder="Cari nomor tagihan, periode, atau tenant"
                                class="h-[50px] w-full rounded-2xl border border-dash-line bg-white pl-11 pr-4 text-[15px] text-dash-ink outline-none transition placeholder:text-dash-soft focus:border-dash-primary focus:ring-4 focus:ring-dash-primary/10"
                            />
                        </div>
                    </div>

                    <div class="w-full sm:w-[240px]">
                        <x-dashboard.form-select
                            name="tenant"
                            label="Tenant"
                            :options="$tenants"
                            :selected="$filters['tenant']"
                            placeholder="Semua Tenant"
                        />
                    </div>

                    <div class="w-full sm:w-[180px]">
                        <x-dashboard.form-select
                            name="status"
                            label="Status"
                            :options="\App\Models\Invoice::STATUS"
                            :selected="$filters['status']"
                            placeholder="Semua Status"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <x-dashboard.button type="submit">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                            </svg>
                            Cari
                        </x-dashboard.button>

                        @if ($isFiltered)
                            <x-dashboard.button variant="secondary" :href="route('tagihan-pembayaran.index')">Reset</x-dashboard.button>
                        @endif
                    </div>
                </form>
            </x-slot:filters>

            <table class="w-full min-w-[1120px] text-left">
                <thead>
                    <tr class="border-y border-dash-line/70 bg-dash-surface/60 text-[13px] uppercase tracking-wide text-dash-muted">
                        <th scope="col" class="px-6 py-4 font-medium lg:px-8">No. Tagihan</th>
                        <th scope="col" class="px-6 py-4 font-medium">Tenant</th>
                        <th scope="col" class="px-6 py-4 font-medium">Periode</th>
                        <th scope="col" class="px-6 py-4 font-medium">Jumlah</th>
                        <th scope="col" class="px-6 py-4 font-medium">Jatuh Tempo</th>
                        <th scope="col" class="px-6 py-4 font-medium">Pembayaran</th>
                        <th scope="col" class="px-6 py-4 font-medium">Status</th>
                        <th scope="col" class="px-6 py-4 text-right font-medium lg:px-8">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-dash-line/70">
                    @forelse ($invoices as $invoice)
                        <tr class="align-middle transition hover:bg-dash-surface/60">
                            <td class="whitespace-nowrap px-6 py-4 text-[15px] font-semibold text-dash-ink lg:px-8">
                                {{ $invoice->nomor_tagihan }}
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-[15px] font-medium text-dash-ink">{{ $invoice->tenant->nama_perusahaan }}</p>
                                <p class="text-[13px] text-dash-muted">{{ $invoice->tenant->nama_pic }}</p>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                {{ $invoice->periodeLabel() }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] font-medium text-dash-ink">
                                {{ $invoice->jumlahRupiah() }}

                                @if ($invoice->catatan)
                                    <span
                                        class="block max-w-[240px] truncate text-[13px] font-normal text-dash-muted"
                                        title="{{ $invoice->catatan }}"
                                    >
                                        {{ $invoice->catatan }}
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                {{ $invoice->jatuh_tempo->format('d/m/Y') }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-[15px] text-dash-ink">
                                @if ($invoice->tanggal_bayar)
                                    {{ $invoice->tanggal_bayar->format('d/m/Y') }}
                                @else
                                    <span class="text-dash-muted">Belum dibayar</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <x-dashboard.badge :tone="$invoice->statusTone()">{{ $invoice->status }}</x-dashboard.badge>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right lg:px-8">
                                <div class="inline-flex items-center gap-2">
                                    @if ($invoice->status !== 'Lunas')
                                        <form method="POST" action="{{ route('tagihan-pembayaran.lunas', $invoice) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rounded-full border border-transparent bg-dash-indigo px-4 py-2 text-[13px] font-medium text-white transition hover:brightness-110"
                                            >
                                                Tandai Lunas
                                            </button>
                                        </form>
                                    @endif

                                    <x-dashboard.table-action :href="route('tagihan-pembayaran.edit', $invoice)">Edit</x-dashboard.table-action>

                                    <x-dashboard.table-action
                                        :href="route('tagihan-pembayaran.destroy', $invoice)"
                                        method="DELETE"
                                        tone="danger"
                                        confirm="Hapus tagihan ini?"
                                    >
                                        Hapus
                                    </x-dashboard.table-action>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-dash-surface text-dash-muted">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </span>

                                <p class="mt-4 text-[15px] font-medium text-dash-ink">
                                    {{ $isFiltered ? 'Tidak ada tagihan yang cocok dengan pencarian.' : 'Belum ada tagihan.' }}
                                </p>
                                <p class="mt-1 text-[13px] text-dash-muted">
                                    {{ $isFiltered ? 'Coba ubah kata kunci atau filter yang digunakan.' : 'Terbitkan tagihan pertama untuk mulai mencatat pembayaran tenant.' }}
                                </p>

                                <div class="mt-5 flex justify-center">
                                    @if ($isFiltered)
                                        <x-dashboard.button variant="secondary" :href="route('tagihan-pembayaran.index')">Reset Filter</x-dashboard.button>
                                    @else
                                        <x-dashboard.button :href="route('tagihan-pembayaran.create')">Terbitkan Tagihan</x-dashboard.button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-dashboard.data-table>
    </div>
</x-layouts.dashboard>
