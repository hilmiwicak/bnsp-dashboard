@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard', 'active' => 'dashboard'],
        ['label' => 'Data Penyewa', 'icon' => 'users', 'route' => 'penyewa.index', 'active' => 'penyewa.*'],
        ['label' => 'Data Ruang & Fasilitas', 'icon' => 'building', 'route' => 'ruang-fasilitas.index', 'active' => 'ruang-fasilitas.*'],
        ['label' => 'Pemesanan', 'icon' => 'receipt', 'route' => 'pemesanan.index', 'active' => 'pemesanan.*'],
        ['label' => 'Kontrak Sewa', 'icon' => 'document', 'route' => 'kontrak-sewa.index', 'active' => 'kontrak-sewa.*'],
        ['label' => 'Jadwal Penggunaan Ruang', 'icon' => 'calendar', 'route' => 'jadwal-ruang.index', 'active' => 'jadwal-ruang.*'],
        ['label' => 'Tagihan & Pembayaran', 'icon' => 'banknotes', 'route' => 'tagihan-pembayaran.index', 'active' => 'tagihan-pembayaran.*'],
        ['label' => 'Laporan', 'icon' => 'chart', 'route' => 'laporan.index', 'active' => 'laporan.*'],
    ];
@endphp

<aside
    data-sidebar
    class="fixed inset-y-0 left-0 z-40 flex w-[248px] -translate-x-full flex-col overflow-y-auto bg-white transition-transform duration-200 ease-out lg:sticky lg:top-0 lg:z-0 lg:h-screen lg:translate-x-0"
>
    <div class="flex items-center gap-3 px-4 pb-8 pt-8">
        <svg class="h-8 w-8 shrink-0 text-dash-indigo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
        </svg>
        <span class="truncate text-[20px] font-bold tracking-tight text-dash-ink">Kantor Virtual.</span>
    </div>

    <nav class="flex flex-1 flex-col gap-1 pb-6" aria-label="Navigasi utama">
        @foreach ($items as $item)
            <x-dashboard.nav-item
                :icon="$item['icon']"
                :href="route($item['route'])"
                :active="request()->routeIs($item['active'])"
            >
                {{ $item['label'] }}
            </x-dashboard.nav-item>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="mt-auto border-t border-dash-line/70">
        @csrf
        <x-dashboard.nav-item icon="logout">Logout</x-dashboard.nav-item>
    </form>
</aside>
