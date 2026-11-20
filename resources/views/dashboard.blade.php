<x-layouts.dashboard title="Dashboard" heading="Ringkasan">
    <div class="space-y-6">
        {{-- Ringkasan angka --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($statistik as $item)
                <x-dashboard.stat-card
                    :label="$item['label']"
                    :value="$item['value']"
                    :hint="$item['hint']"
                    :icon="$item['icon']"
                    :tone="$item['tone']"
                />
            @endforeach
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Aktivitas pemesanan --}}
            <section class="flex flex-col lg:col-span-8">
                <x-dashboard.section-title title="Aktivitas Pemesanan">
                    <x-slot:action>
                        <a href="{{ route('pemesanan.index') }}" class="text-[15px] font-semibold text-dash-primary transition hover:text-dash-blue">Lihat Semua</a>
                    </x-slot:action>
                </x-dashboard.section-title>

                <div class="flex-1 rounded-[20px] bg-white px-6 pb-4 pt-5 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-2">
                        <p class="text-[13px] text-dash-muted">Tujuh hari terakhir</p>

                        <div class="flex items-center gap-6">
                            @foreach ($aktivitas['series'] as $serie)
                                <span class="flex items-center gap-2 text-[13px] text-dash-muted">
                                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $serie['color'] }}"></span>
                                    {{ $serie['name'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <x-dashboard.bar-chart :labels="$aktivitas['labels']" :series="$aktivitas['series']" />
                </div>
            </section>

            {{-- Komposisi status tenant --}}
            <section class="flex flex-col lg:col-span-4">
                <x-dashboard.section-title title="Status Tenant" />

                <div class="flex flex-1 flex-col justify-center gap-6 rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                    @if ($statusTenant === [])
                        <div class="py-8 text-center">
                            <p class="text-[15px] font-medium text-dash-ink">Belum ada tenant.</p>
                            <p class="mt-1 text-[13px] text-dash-muted">Tambahkan data tenant untuk melihat komposisinya di sini.</p>

                            <div class="mt-5 flex justify-center">
                                <x-dashboard.button :href="route('tenants.create')">Tambah Tenant</x-dashboard.button>
                            </div>
                        </div>
                    @else
                        <x-dashboard.pie-chart :slices="$statusTenant" />

                        <ul class="space-y-3">
                            @foreach ($statusTenant as $slice)
                                <li class="flex items-center gap-3">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $slice['color'] }}"></span>
                                    <span class="min-w-0 flex-1 truncate text-[14px] text-dash-ink">{{ $slice['label'] }}</span>
                                    <span class="shrink-0 text-[14px] font-semibold text-dash-ink">{{ $slice['value'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>

            {{-- Riwayat pendapatan --}}
            <section class="flex flex-col lg:col-span-7">
                <x-dashboard.section-title title="Riwayat Pendapatan">
                    <x-slot:action>
                        <a href="{{ route('tagihan-pembayaran.index') }}" class="text-[15px] font-semibold text-dash-primary transition hover:text-dash-blue">Lihat Tagihan</a>
                    </x-slot:action>
                </x-dashboard.section-title>

                <div class="flex flex-1 flex-col justify-center rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                    <x-dashboard.line-chart :labels="$pendapatan['labels']" :values="$pendapatan['values']" class="w-full" />

                    <p class="mt-3 text-[13px] text-dash-muted">Dalam juta rupiah, dihitung dari tagihan berstatus lunas.</p>
                </div>
            </section>

            {{-- Tenant terbaru --}}
            <section class="flex flex-col lg:col-span-5">
                <x-dashboard.section-title title="Tenant Terbaru">
                    <x-slot:action>
                        <a href="{{ route('tenants.index') }}" class="text-[15px] font-semibold text-dash-primary transition hover:text-dash-blue">Lihat Semua</a>
                    </x-slot:action>
                </x-dashboard.section-title>

                <div class="flex flex-1 flex-col justify-center gap-4 rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                    @forelse ($tenantTerbaru as $tenant)
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#e7edff] text-[13px] font-semibold text-[#396aff]">
                                {{ $tenant->inisial() ?: 'TN' }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[15px] font-medium text-dash-ink">{{ $tenant->nama_perusahaan }}</p>
                                <p class="truncate text-[13px] text-dash-muted">{{ $tenant->kode_tenant }} &middot; {{ $tenant->nama_pic }}</p>
                            </div>

                            <x-dashboard.badge :tone="$tenant->statusTone()">{{ $tenant->status }}</x-dashboard.badge>
                        </div>
                    @empty
                        <div class="py-6 text-center">
                            <p class="text-[15px] font-medium text-dash-ink">Belum ada tenant terdaftar.</p>
                            <p class="mt-1 text-[13px] text-dash-muted">Data tenant yang baru ditambahkan akan muncul di sini.</p>

                            <div class="mt-5 flex justify-center">
                                <x-dashboard.button :href="route('tenants.create')">Tambah Tenant</x-dashboard.button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts.dashboard>
