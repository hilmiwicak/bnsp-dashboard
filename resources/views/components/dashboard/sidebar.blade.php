@php
    $isAdmin = auth()->user()?->isAdmin() ?? false;

    $adminItems = [
        ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard', 'active' => 'dashboard'],
        ['label' => 'Data Tenant', 'icon' => 'users', 'route' => 'tenants.index', 'active' => 'tenants.*'],
        ['label' => 'Data Ruang & Fasilitas', 'icon' => 'building', 'route' => 'ruang-fasilitas.index', 'active' => 'ruang-fasilitas.*'],
        ['label' => 'Pemesanan', 'icon' => 'receipt', 'route' => 'pemesanan.index', 'active' => 'pemesanan.*'],
        ['label' => 'Kontrak Sewa', 'icon' => 'document', 'route' => 'kontrak-sewa.index', 'active' => 'kontrak-sewa.*'],
        ['label' => 'Jadwal Penggunaan Ruang', 'icon' => 'calendar', 'route' => 'jadwal-ruang.index', 'active' => 'jadwal-ruang.*'],
        ['label' => 'Tagihan & Pembayaran', 'icon' => 'banknotes', 'route' => 'tagihan-pembayaran.index', 'active' => 'tagihan-pembayaran.*'],
        ['label' => 'Laporan', 'icon' => 'chart', 'route' => 'laporan.index', 'active' => 'laporan.*'],
    ];

    $portalItems = [
        ['label' => 'Portal', 'icon' => 'home', 'route' => 'portal', 'active' => 'portal'],
        ['label' => 'Ruang & Fasilitas', 'icon' => 'building', 'route' => 'portal.ruang', 'active' => 'portal.ruang*'],
    ];

    $items = $isAdmin ? $adminItems : $portalItems;
@endphp

<aside
    data-sidebar
    class="fixed inset-y-0 left-0 z-40 flex w-[248px] -translate-x-full flex-col overflow-y-auto bg-white transition-transform duration-200 ease-out lg:sticky lg:top-0 lg:z-0 lg:h-screen lg:translate-x-0"
>
    <div class="flex items-center gap-3 px-4 pb-8 pt-8">
        <x-logo class="h-10 w-10" />
        <span class="truncate text-[20px] font-bold tracking-tight text-dash-ink">Kantor Virtual.</span>
    </div>

    <nav class="flex flex-1 flex-col gap-1 overflow-y-auto pb-6" aria-label="Navigasi utama">
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

    <form method="POST" action="{{ route('logout') }}" class="mt-auto shrink-0 border-t border-dash-line/70">
        @csrf
        <x-dashboard.nav-item icon="logout">Logout</x-dashboard.nav-item>
    </form>
</aside>
