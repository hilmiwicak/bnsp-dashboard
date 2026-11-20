@php
    $statusTone = [
        'Aktif' => 'bg-[#dcfaf8] text-[#0f9b8f]',
        'Menunggu Verifikasi' => 'bg-[#fff5d1] text-[#b57e00]',
        'Nonaktif' => 'bg-[#f2f4f7] text-dash-muted',
    ];

    $avatarTone = [
        'bg-[#e7edff] text-[#396aff]',
        'bg-[#dcfaf8] text-[#16dbcc]',
        'bg-[#fff5d1] text-[#ffbb38]',
        'bg-[#fde9f1] text-[#ff82ac]',
    ];

    $isFiltered = $filters['q'] !== '' || $filters['status'];
@endphp

<x-layouts.dashboard title="Data Tenant" heading="Data Tenant">
    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success">{{ session('status') }}</x-alert>
        @endif

        <section class="rounded-[20px] bg-white shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-dash-line/70 px-6 py-5 lg:px-8">
                <div>
                    <h2 class="text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">Daftar Tenant</h2>
                    <p class="mt-1 text-[13px] text-dash-muted">
                        {{ $totalSemua }} tenant terdaftar pada kantor virtual.
                    </p>
                </div>

                <x-dashboard.button :href="route('tenants.create')" class="shrink-0">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Tenant
                </x-dashboard.button>
            </div>

            <form method="GET" action="{{ route('tenants.index') }}" class="flex flex-wrap items-end gap-3 px-6 py-5 lg:px-8">
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
                            placeholder="Cari kode, nama PIC, perusahaan, atau email"
                            class="h-[50px] w-full rounded-2xl border border-dash-line bg-white pl-11 pr-4 text-[15px] text-dash-ink outline-none transition placeholder:text-dash-soft focus:border-dash-primary focus:ring-4 focus:ring-dash-primary/10"
                        />
                    </div>
                </div>

                <div class="w-full sm:w-[180px]">
                    <x-dashboard.form-select
                        name="status"
                        label="Status"
                        :options="\App\Models\Tenant::STATUS"
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
                        <x-dashboard.button variant="secondary" :href="route('tenants.index')">Reset</x-dashboard.button>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead>
                        <tr class="border-y border-dash-line/70 bg-dash-surface/60 text-[13px] uppercase tracking-wide text-dash-muted">
                            <th scope="col" class="px-6 py-4 font-medium lg:px-8">Kode</th>
                            <th scope="col" class="px-6 py-4 font-medium">Tenant</th>
                            <th scope="col" class="px-6 py-4 font-medium">Kontak</th>
                            <th scope="col" class="px-6 py-4 font-medium">Masa Sewa</th>
                            <th scope="col" class="px-6 py-4 font-medium">Status</th>
                            <th scope="col" class="px-6 py-4 text-right font-medium lg:px-8">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-dash-line/70">
                        @forelse ($tenant as $item)
                            <tr class="align-middle transition hover:bg-dash-surface/60">
                                <td class="whitespace-nowrap px-6 py-4 text-[15px] font-semibold text-dash-ink lg:px-8">
                                    {{ $item->kode_tenant }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-[13px] font-semibold {{ $avatarTone[$loop->index % count($avatarTone)] }}">
                                            {{ $item->inisial() }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-[15px] font-medium text-dash-ink">{{ $item->nama_pic }}</p>
                                            <p class="truncate text-[13px] text-dash-muted">{{ $item->nama_perusahaan }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="truncate text-[15px] text-dash-ink">{{ $item->email }}</p>
                                    <p class="text-[13px] text-dash-muted">{{ $item->no_hp }}</p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-dash-ink">
                                    {{ $item->tanggal_mulai_sewa->format('d/m/Y') }}
                                    <span class="block text-[13px] text-dash-muted">
                                        {{ $item->durasi_sewa }} bulan &middot; s.d. {{ $item->masaSewaBerakhir()->format('d/m/Y') }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-[13px] font-semibold {{ $statusTone[$item->status] ?? 'bg-dash-surface text-dash-muted' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right lg:px-8">
                                    <div class="inline-flex items-center gap-2">
                                        <a
                                            href="{{ route('tenants.edit', $item) }}"
                                            class="rounded-full border border-dash-line px-4 py-2 text-[13px] font-medium text-dash-ink transition hover:border-dash-primary hover:text-dash-primary"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('tenants.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus data tenant ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="rounded-full border border-rose-200 px-4 py-2 text-[13px] font-medium text-rose-600 transition hover:border-rose-400 hover:bg-rose-50"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-dash-surface text-dash-muted">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                        </svg>
                                    </span>

                                    <p class="mt-4 text-[15px] font-medium text-dash-ink">
                                        {{ $isFiltered ? 'Tidak ada tenant yang cocok dengan pencarian.' : 'Belum ada data tenant.' }}
                                    </p>
                                    <p class="mt-1 text-[13px] text-dash-muted">
                                        {{ $isFiltered ? 'Coba ubah kata kunci atau filter yang digunakan.' : 'Tambahkan tenant pertama untuk mulai mengelola data kantor virtual.' }}
                                    </p>

                                    <div class="mt-5 flex justify-center">
                                        @if ($isFiltered)
                                            <x-dashboard.button variant="secondary" :href="route('tenants.index')">Reset Filter</x-dashboard.button>
                                        @else
                                            <x-dashboard.button :href="route('tenants.create')">Tambah Tenant</x-dashboard.button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tenant->total() > 0)
                <div class="flex flex-wrap items-center justify-between gap-4 border-t border-dash-line/70 px-6 py-4 lg:px-8">
                    <p class="text-[13px] text-dash-muted">
                        Menampilkan {{ $tenant->firstItem() }}&ndash;{{ $tenant->lastItem() }} dari {{ $tenant->total() }} tenant
                    </p>

                    {{ $tenant->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.dashboard>
