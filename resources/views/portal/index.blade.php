@php
    $user = $user ?? auth()->user();
    $tenant = $user->tenant;

    // Akun tenant yang belum punya data sewa dianggap menunggu verifikasi
    // sampai admin membuatkan data tenant.
    $status = $tenant?->status ?? 'Menunggu Verifikasi';

    $states = [
        'Aktif' => [
            'badge' => 'bg-[#dcfaf8] text-[#0f9b8f]',
            'dot' => 'bg-[#16dbcc]',
            'title' => 'Keanggotaan aktif',
            'description' => 'Masa sewa Anda berjalan normal. Seluruh fasilitas kantor virtual dapat digunakan.',
        ],
        'Menunggu Verifikasi' => [
            'badge' => 'bg-[#fff5d1] text-[#b57e00]',
            'dot' => 'bg-[#ffbb38]',
            'title' => 'Menunggu verifikasi admin',
            'description' => 'Pengajuan sewa Anda sedang ditinjau. Kami akan menghubungi Anda setelah data selesai diverifikasi.',
        ],
        'Nonaktif' => [
            'badge' => 'bg-[#f2f4f7] text-dash-muted',
            'dot' => 'bg-dash-soft',
            'title' => 'Keanggotaan nonaktif',
            'description' => 'Masa sewa telah berakhir. Hubungi admin kantor virtual untuk memperpanjang keanggotaan.',
        ],
    ];

    $state = $states[$status] ?? $states['Menunggu Verifikasi'];

    if (! $tenant) {
        $state['title'] = 'Pengajuan sewa belum dibuat';
        $state['description'] = 'Akun Anda sudah aktif. Lengkapi pengajuan sewa agar dapat diverifikasi oleh admin kantor virtual.';
    }

    $initials = collect(explode(' ', (string) $user->name))
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');

    // Periode sewa yang ditampilkan mengikuti kontrak terbaru (aktif atau
    // menunggu verifikasi), bukan input tenant.
    $currentContract = $tenant?->currentContract;

    $details = $tenant ? [
        'Kode Tenant' => $tenant->kode_tenant,
        'Perusahaan' => $tenant->nama_perusahaan,
        'PIC' => $tenant->nama_pic,
        'Mulai Sewa' => $currentContract?->tanggal_mulai?->format('d M Y'),
        'Durasi' => $currentContract ? $currentContract->durasiBulan().' bulan' : null,
        'Berakhir' => $currentContract?->tanggal_berakhir?->format('d M Y'),
    ] : [];

    $steps = [
        'Lengkapi data perusahaan, PIC, dan kontak penanggung jawab.',
        'Tunggu verifikasi dari admin kantor virtual.',
        'Mulai gunakan ruang dan fasilitas kantor virtual.',
    ];
@endphp

<x-layouts.dashboard title="Portal" heading="Portal">
    <div class="space-y-6">
        @if (session('status'))
            <x-alert type="success">{{ session('status') }}</x-alert>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <section class="lg:col-span-8">
                <div class="h-full rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)] lg:p-8">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#ece7fe] text-[20px] font-semibold text-[#6f34e0]">
                            {{ $initials ?: 'KV' }}
                        </span>

                        <div class="min-w-0">
                            <h2 class="truncate text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">{{ $user->name }}</h2>
                            <p class="truncate text-[15px] text-dash-muted">{{ $user->email }}</p>
                        </div>

                        <span class="ml-auto shrink-0 rounded-full bg-dash-surface px-4 py-1.5 text-[13px] font-semibold text-dash-ink">
                            {{ $user->role?->label() }}
                        </span>
                    </div>

                    <div class="mt-8 rounded-2xl border border-dash-line/70 bg-dash-surface/60 p-5 lg:p-6">
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[13px] font-semibold {{ $state['badge'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $state['dot'] }}"></span>
                            {{ $status }}
                        </span>

                        <h3 class="mt-3 text-[17px] font-semibold text-dash-ink">{{ $state['title'] }}</h3>
                        <p class="mt-1 max-w-[560px] text-[14px] leading-relaxed text-dash-muted">{{ $state['description'] }}</p>

                        @if ($tenant)
                            <div class="mt-5">
                                <x-dashboard.button :href="route('portal.ruang')">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                    Pesan Ruang
                                </x-dashboard.button>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="lg:col-span-4">
                <div class="h-full rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)] lg:p-8">
                    @if ($details)
                        <h2 class="text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">Data Sewa</h2>

                        <dl class="mt-5 space-y-4">
                            @foreach ($details as $label => $value)
                                @continue (blank($value))

                                <div class="flex items-start justify-between gap-4 border-b border-dash-line/70 pb-4 last:border-0 last:pb-0">
                                    <dt class="text-[13px] text-dash-muted">{{ $label }}</dt>
                                    <dd class="text-right text-[14px] font-medium text-dash-ink">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @else
                        <h2 class="text-[20px] font-semibold tracking-tight text-dash-ink lg:text-[22px]">Langkah Selanjutnya</h2>

                        <ol class="mt-5 space-y-4">
                            @foreach ($steps as $step)
                                <li class="flex gap-3">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-dash-surface text-[12px] font-semibold text-dash-ink">
                                        {{ $loop->iteration }}
                                    </span>
                                    <p class="text-[14px] leading-relaxed text-dash-muted">{{ $step }}</p>
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-6">
                            <x-dashboard.button :href="route('portal.apply')" class="w-full">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Ajukan Sewa
                            </x-dashboard.button>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-layouts.dashboard>
