@php
    $weeklyLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

    $weeklySeries = [
        ['name' => 'Pemesanan', 'color' => '#3b37e0', 'values' => [490, 350, 330, 480, 160, 390, 400]],
        ['name' => 'Pembatalan', 'color' => '#16dbcc', 'values' => [240, 120, 260, 360, 240, 250, 340]],
    ];

    $expenseSlices = [
        ['label' => 'Ruang Meeting', 'value' => 30, 'color' => '#343c6a'],
        ['label' => 'Layanan Tambahan', 'value' => 15, 'color' => '#fc7900'],
        ['label' => 'Lainnya', 'value' => 35, 'color' => '#1814f3'],
        ['label' => 'Keanggotaan', 'value' => 20, 'color' => '#fa00ff'],
    ];

    $balanceLabels = ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan'];
    $balanceValues = [120, 320, 480, 780, 220, 570, 620];

    $transactions = [
        [
            'title' => 'Perpanjangan Paket Kantor',
            'date' => '28 Januari 2021',
            'amount' => '-Rp850.000',
            'tone' => 'bg-[#fff5d1] text-[#ffbb38]',
            'amountTone' => 'text-dash-alert',
            'icon' => 'card',
        ],
        [
            'title' => 'Pemesanan Ruang Meeting',
            'date' => '25 Januari 2021',
            'amount' => '+Rp2.500.000',
            'tone' => 'bg-[#e7edff] text-[#396aff]',
            'amountTone' => 'text-[#41d2a4]',
            'icon' => 'doc',
        ],
        [
            'title' => 'Layanan Surat Menyurat',
            'date' => '21 Januari 2021',
            'amount' => '+Rp5.400.000',
            'tone' => 'bg-[#dcfaf8] text-[#16dbcc]',
            'amountTone' => 'text-[#41d2a4]',
            'icon' => 'user',
        ],
    ];

    $team = [
        ['name' => 'Livia Bator', 'role' => 'Manajer', 'initials' => 'LB', 'tone' => 'bg-[#e7edff] text-[#396aff]'],
        ['name' => 'Randy Press', 'role' => 'Resepsionis', 'initials' => 'RP', 'tone' => 'bg-[#dcfaf8] text-[#16dbcc]'],
        ['name' => 'Workman', 'role' => 'Staf Operasional', 'initials' => 'WK', 'tone' => 'bg-[#fff5d1] text-[#ffbb38]'],
    ];

    $transactionIcons = [
        'card' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 8.25v9.75a1.5 1.5 0 0 0 1.5 1.5h16.5a1.5 1.5 0 0 0 1.5-1.5V8.25M2.25 8.25V6a1.5 1.5 0 0 1 1.5-1.5h16.5A1.5 1.5 0 0 1 21.75 6v2.25" />',
        'doc' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />',
    ];
@endphp

<x-layouts.dashboard title="Dashboard" heading="Ringkasan">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Kartu keanggotaan --}}
        <section class="flex flex-col lg:col-span-8">
            <x-dashboard.section-title title="Kartu Keanggotaan" />

            <div class="grid flex-1 gap-7 sm:grid-cols-2">
                <x-dashboard.membership-card />
                <x-dashboard.membership-card
                    variant="plain"
                    balance="Rp3.120.000"
                    holder="Siti Rahmawati"
                    valid="08/26"
                    number="3778 **** **** 9082"
                />
            </div>
        </section>

        {{-- Transaksi terbaru --}}
        <section class="flex flex-col lg:col-span-4">
            <x-dashboard.section-title title="Transaksi Terbaru" />

            <div class="flex flex-1 flex-col justify-between gap-2 rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                @foreach ($transactions as $transaction)
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $transaction['tone'] }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                {!! $transactionIcons[$transaction['icon']] !!}
                            </svg>
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[15px] font-medium text-dash-ink">{{ $transaction['title'] }}</p>
                            <p class="mt-0.5 text-xs text-dash-muted">{{ $transaction['date'] }}</p>
                        </div>

                        <p class="shrink-0 text-[15px] font-medium {{ $transaction['amountTone'] }}">{{ $transaction['amount'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Aktivitas mingguan --}}
        <section class="flex flex-col lg:col-span-8">
            <x-dashboard.section-title title="Aktivitas Mingguan" />

            <div class="flex-1 rounded-[20px] bg-white px-6 pb-4 pt-5 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                <div class="flex items-center justify-end gap-6 pb-2">
                    @foreach ($weeklySeries as $serie)
                        <span class="flex items-center gap-2 text-[13px] text-dash-muted">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $serie['color'] }}"></span>
                            {{ $serie['name'] }}
                        </span>
                    @endforeach
                </div>

                <x-dashboard.bar-chart :labels="$weeklyLabels" :series="$weeklySeries" />
            </div>
        </section>

        {{-- Statistik pengeluaran --}}
        <section class="flex flex-col lg:col-span-4">
            <x-dashboard.section-title title="Statistik Pengeluaran" />

            <div class="flex flex-1 items-center justify-center rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                <x-dashboard.pie-chart :slices="$expenseSlices" />
            </div>
        </section>

        {{-- Catat pembayaran --}}
        <section class="flex flex-col lg:col-span-5">
            <x-dashboard.section-title title="Catat Pembayaran" />

            <div class="flex flex-1 flex-col justify-between gap-8 rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                <div class="flex items-start gap-4 sm:gap-6">
                    @foreach ($team as $person)
                        <div class="flex min-w-0 flex-col items-center gap-2 text-center">
                            <span class="flex h-[60px] w-[60px] items-center justify-center rounded-full text-base font-semibold {{ $person['tone'] }}">
                                {{ $person['initials'] }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-[15px] font-medium text-dash-ink">{{ $person['name'] }}</p>
                                <p class="truncate text-xs text-dash-muted">{{ $person['role'] }}</p>
                            </div>
                        </div>
                    @endforeach

                    <button
                        type="button"
                        class="ml-auto flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white text-dash-muted shadow-[0_2px_12px_rgba(25,35,80,0.12)] transition hover:text-dash-primary"
                        aria-label="Tampilkan petugas lain"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>

                <form class="flex flex-wrap items-center gap-4">
                    <label for="payment-amount" class="text-[15px] text-dash-muted">Jumlah</label>

                    <div class="relative min-w-[190px] flex-1">
                        <input
                            id="payment-amount"
                            type="text"
                            inputmode="decimal"
                            placeholder="525.000"
                            class="h-14 w-full rounded-full bg-dash-surface pl-6 pr-32 text-[15px] text-dash-ink outline-none placeholder:text-dash-soft focus:ring-2 focus:ring-dash-primary/20"
                        />
                        <button
                            type="submit"
                            class="absolute right-1.5 top-1.5 flex items-center gap-2 rounded-full bg-dash-indigo px-6 py-3 text-[15px] font-medium text-white transition hover:brightness-110"
                        >
                            Simpan
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Riwayat pendapatan --}}
        <section class="flex flex-col lg:col-span-7">
            <x-dashboard.section-title title="Riwayat Pendapatan" />

            <div class="flex flex-1 items-center rounded-[20px] bg-white p-6 shadow-[0_4px_20px_rgba(25,35,80,0.03)]">
                <x-dashboard.line-chart :labels="$balanceLabels" :values="$balanceValues" class="w-full" />
            </div>
        </section>
    </div>
</x-layouts.dashboard>
