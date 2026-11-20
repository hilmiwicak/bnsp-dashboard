<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Support\Bulan;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Warna tetap untuk setiap status tenant pada diagram.
     *
     * @var array<string, string>
     */
    private const WARNA_STATUS = [
        'Aktif' => '#3434e8',
        'Menunggu Verifikasi' => '#ffbb38',
        'Nonaktif' => '#8ba3cb',
    ];

    /**
     * Ringkasan kantor virtual dari data yang tersimpan di basis data.
     */
    public function index(): View
    {
        $statusTenant = Tenant::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalTenant = (int) $statusTenant->sum();

        $pemesananMingguIni = Booking::query()
            ->whereDate('tanggal', '>=', now()->subDays(6)->toDateString())
            ->whereDate('tanggal', '<=', now()->toDateString())
            ->count();

        $pendapatanLunas = (int) Invoice::query()->where('status', 'Lunas')->sum('jumlah');
        $tagihanTerbuka = (int) Invoice::query()->belumLunas()->sum('jumlah');

        return view('dashboard', [
            'statistik' => [
                [
                    'label' => 'Total Tenant',
                    'value' => (string) $totalTenant,
                    'hint' => ($statusTenant['Aktif'] ?? 0).' tenant aktif',
                    'icon' => 'users',
                    'tone' => 'blue',
                ],
                [
                    'label' => 'Menunggu Verifikasi',
                    'value' => (string) ($statusTenant['Menunggu Verifikasi'] ?? 0),
                    'hint' => 'Perlu ditinjau admin',
                    'icon' => 'clock',
                    'tone' => 'amber',
                ],
                [
                    'label' => 'Pemesanan 7 Hari',
                    'value' => (string) $pemesananMingguIni,
                    'hint' => 'Termasuk yang menunggu',
                    'icon' => 'calendar',
                    'tone' => 'teal',
                ],
                [
                    'label' => 'Pendapatan Lunas',
                    'value' => $this->rupiah($pendapatanLunas),
                    'hint' => $this->rupiah($tagihanTerbuka).' belum lunas',
                    'icon' => 'banknotes',
                    'tone' => 'indigo',
                ],
            ],
            'statusTenant' => $this->slicesStatusTenant($statusTenant),
            'aktivitas' => $this->aktivitasMingguan(),
            'pendapatan' => $this->pendapatanBulanan(),
            'tenantTerbaru' => Tenant::query()->latest()->take(5)->get(),
        ]);
    }

    /**
     * Komposisi status tenant untuk diagram lingkaran.
     *
     * @param  Collection<string, int>  $statusTenant
     * @return list<array{label: string, value: int, color: string}>
     */
    private function slicesStatusTenant(Collection $statusTenant): array
    {
        $slices = [];

        foreach (Tenant::STATUS as $status) {
            $total = (int) ($statusTenant[$status] ?? 0);

            if ($total === 0) {
                continue;
            }

            $slices[] = [
                'label' => $status,
                'value' => $total,
                'color' => self::WARNA_STATUS[$status] ?? '#8ba3cb',
            ];
        }

        return $slices;
    }

    /**
     * Jumlah pemesanan per hari selama tujuh hari terakhir.
     *
     * @return array{labels: list<string>, series: list<array{name: string, color: string, values: list<int>}>}
     */
    private function aktivitasMingguan(): array
    {
        $perTanggal = Booking::query()
            ->whereDate('tanggal', '>=', now()->subDays(6)->toDateString())
            ->whereDate('tanggal', '<=', now()->toDateString())
            ->get(['tanggal', 'status'])
            ->groupBy(fn (Booking $booking) => $booking->tanggal->toDateString());

        $labels = [];
        $disetujui = [];
        $menunggu = [];

        for ($hari = 6; $hari >= 0; $hari--) {
            $tanggal = now()->subDays($hari);
            $items = $perTanggal->get($tanggal->toDateString(), collect());

            $labels[] = Bulan::singkatHari($tanggal->dayOfWeek);
            $disetujui[] = $items->whereIn('status', ['Disetujui', 'Selesai'])->count();
            $menunggu[] = $items->where('status', 'Menunggu')->count();
        }

        return [
            'labels' => $labels,
            'series' => [
                ['name' => 'Disetujui', 'color' => '#3434e8', 'values' => $disetujui],
                ['name' => 'Menunggu', 'color' => '#16dbcc', 'values' => $menunggu],
            ],
        ];
    }

    /**
     * Total pembayaran lunas per bulan selama enam bulan terakhir.
     *
     * Nilai dikirim dalam satuan juta rupiah agar sumbu grafik tetap terbaca.
     *
     * @return array{labels: list<string>, values: list<float>}
     */
    private function pendapatanBulanan(): array
    {
        $awal = now()->startOfMonth()->subMonths(5);

        $perBulan = Invoice::query()
            ->where('status', 'Lunas')
            ->whereNotNull('tanggal_bayar')
            ->whereDate('tanggal_bayar', '>=', $awal->toDateString())
            ->get(['tanggal_bayar', 'jumlah'])
            ->groupBy(fn (Invoice $invoice) => $invoice->tanggal_bayar->format('Y-m'));

        $labels = [];
        $values = [];

        for ($bulan = 5; $bulan >= 0; $bulan--) {
            $periode = $awal->copy()->addMonths(5 - $bulan);
            $items = $perBulan->get($periode->format('Y-m'), collect());

            $labels[] = Bulan::singkat($periode->month);
            $values[] = round((int) $items->sum('jumlah') / 1000000, 1);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Format rupiah tanpa desimal.
     */
    private function rupiah(int $nilai): string
    {
        return 'Rp'.number_format($nilai, 0, ',', '.');
    }
}
