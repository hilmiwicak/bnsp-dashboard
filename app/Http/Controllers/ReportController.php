<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Support\Bulan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Laporan tahunan: pemesanan, pendapatan, dan tagihan per bulan.
     *
     * Modul ini hanya-baca; seluruh angka dihitung dari data yang sudah ada.
     */
    public function index(Request $request): View
    {
        $tahun = $this->resolveTahun($request->query('tahun'));
        $tahunTersedia = $this->tahunTersedia($tahun);

        $pemesanan = Booking::query()
            ->whereYear('tanggal', $tahun)
            ->get(['tanggal', 'status']);

        $pemesananPerBulan = $pemesanan->groupBy(fn (Booking $booking) => (int) $booking->tanggal->month);

        $lunasPerBulan = Invoice::query()
            ->where('status', 'Lunas')
            ->whereNotNull('tanggal_bayar')
            ->whereYear('tanggal_bayar', $tahun)
            ->get(['tanggal_bayar', 'jumlah'])
            ->groupBy(fn (Invoice $invoice) => (int) $invoice->tanggal_bayar->month);

        $belumLunasPerBulan = Invoice::query()
            ->belumLunas()
            ->where('periode', 'like', $tahun.'-%')
            ->get(['periode', 'jumlah'])
            ->groupBy(fn (Invoice $invoice) => (int) substr((string) $invoice->periode, 5, 2));

        $baris = [];

        foreach (range(1, 12) as $bulan) {
            $baris[] = [
                'bulan' => Bulan::nama($bulan),
                'pemesanan' => $pemesananPerBulan->get($bulan, collect())->count(),
                'pendapatan' => (int) $lunasPerBulan->get($bulan, collect())->sum('jumlah'),
                'belum_lunas' => (int) $belumLunasPerBulan->get($bulan, collect())->sum('jumlah'),
            ];
        }

        $statusPemesanan = collect(Booking::STATUS)
            ->map(fn (string $status) => [
                'status' => $status,
                'jumlah' => $pemesanan->where('status', $status)->count(),
                'tone' => Booking::toneFor($status),
            ]);

        return view('reports.index', [
            'tahun' => $tahun,
            'tahunTersedia' => $tahunTersedia,
            'baris' => $baris,
            'statusPemesanan' => $statusPemesanan,
            'totalPemesanan' => $pemesanan->count(),
            'totalPendapatan' => (int) $lunasPerBulan->flatten()->sum('jumlah'),
            'totalBelumLunas' => (int) $belumLunasPerBulan->flatten()->sum('jumlah'),
        ]);
    }

    /**
     * Tahun laporan yang dipilih, dibatasi pada rentang yang masuk akal.
     */
    private function resolveTahun(mixed $value): int
    {
        $tahun = (int) $value;

        return $tahun >= 2000 && $tahun <= 2100 ? $tahun : now()->year;
    }

    /**
     * Pilihan tahun: tahun yang ada datanya, ditambah tahun yang sedang dilihat.
     *
     * @return Collection<int, int>
     */
    private function tahunTersedia(int $tahun): Collection
    {
        $tahunInvoice = Invoice::query()
            ->pluck('periode')
            ->map(fn ($periode) => (int) substr((string) $periode, 0, 4));

        $tahunPemesanan = Booking::query()
            ->pluck('tanggal')
            ->map(fn ($tanggal) => Carbon::parse($tanggal)->year);

        return $tahunInvoice
            ->merge($tahunPemesanan)
            ->push($tahun)
            ->push(now()->year)
            ->filter(fn (int $item) => $item >= 2000)
            ->unique()
            ->sortDesc()
            ->values();
    }
}
