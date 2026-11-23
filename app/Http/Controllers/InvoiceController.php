<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Support\TagihanRuang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Batas jumlah bulan yang boleh diterbitkan sekaligus.
     */
    private const MAKS_BULAN = 24;

    /**
     * Tampilkan daftar tagihan beserta pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $tenantFilter = $request->integer('tenant') ?: null;

        $filters = [
            'q' => trim((string) $request->query('q')),
            'status' => $this->allowedFilter($request->query('status'), Invoice::STATUS),
            'tenant' => $tenantFilter && Tenant::whereKey($tenantFilter)->exists() ? $tenantFilter : null,
        ];

        $invoices = Invoice::query()
            ->with('tenant')
            ->search($filters['q'])
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['tenant'], fn ($query, $tenant) => $query->where('tenant_id', $tenant))
            ->orderByDesc('periode')
            ->orderBy('nomor_tagihan')
            ->paginate(8)
            ->withQueryString();

        return view('invoices.index', [
            'invoices' => $invoices,
            'filters' => $filters,
            'tenants' => $this->tenantOptions(),
            'totalSemua' => Invoice::count(),
            'totalBelumLunas' => Invoice::query()->belumLunas()->sum('jumlah'),
        ]);
    }

    /**
     * Tampilkan formulir tambah tagihan.
     */
    public function create(Request $request): View
    {
        $tenantId = $request->integer('tenant_id') ?: null;
        $periode = trim((string) $request->query('periode'));
        $periodeAkhir = trim((string) $request->query('periode_akhir'));

        $periodeValid = self::periodeValid($periode);
        $akhirValid = self::periodeValid($periodeAkhir);

        $siapHitung = $tenantId !== null && $periodeValid;

        $saran = $siapHitung ? TagihanRuang::ringkasan($tenantId, $periode) : null;

        $duplikat = $siapHitung
            ? Invoice::query()->where('tenant_id', $tenantId)->where('periode', $periode)->count()
            : 0;

        // Rentang hanya dihitung bila bulan akhir berada setelah bulan awal.
        $rentang = null;
        $totalRentang = 0;
        $periodeAda = [];

        if ($siapHitung && $akhirValid && $periodeAkhir > $periode) {
            $rentang = TagihanRuang::rentang($tenantId, $periode, $periodeAkhir);
            $totalRentang = (int) array_sum(array_column($rentang, 'total'));

            $periodeAda = Invoice::query()
                ->where('tenant_id', $tenantId)
                ->whereIn('periode', array_column($rentang, 'periode'))
                ->pluck('periode')
                ->all();
        }

        return view('invoices.create', [
            'invoice' => new Invoice([
                'nomor_tagihan' => Invoice::nextKodeTagihan(),
                'tenant_id' => $tenantId,
                'periode' => $periodeValid ? $periode : now()->format('Y-m'),
                'jumlah' => $saran['total'] ?? 0,
                'jatuh_tempo' => now()->endOfMonth(),
                'status' => 'Belum Dibayar',
            ]),
            'tenants' => $this->tenantOptions(),
            'saran' => $saran,
            'duplikat' => $duplikat,
            'rentang' => $rentang,
            'totalRentang' => $totalRentang,
            'periodeAda' => $periodeAda,
            'filter' => [
                'tenant_id' => $tenantId,
                'periode' => $periodeValid ? $periode : '',
                'periode_akhir' => $akhirValid ? $periodeAkhir : '',
            ],
        ]);
    }

    /**
     * Terbitkan satu tagihan untuk setiap bulan pada rentang yang dipilih.
     */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'periode_awal' => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'periode_akhir' => [
                'required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/',
                'after_or_equal:periode_awal',
            ],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'periode_akhir.after_or_equal' => 'Periode akhir tidak boleh sebelum periode awal.',
        ]);

        $awal = Carbon::createFromFormat('Y-m', $validated['periode_awal'])->startOfMonth();
        $akhir = Carbon::createFromFormat('Y-m', $validated['periode_akhir'])->startOfMonth();

        $jumlahBulan = (($akhir->year - $awal->year) * 12) + ($akhir->month - $awal->month) + 1;

        if ($jumlahBulan > self::MAKS_BULAN) {
            return back()->withErrors([
                'periode_akhir' => 'Rentang maksimal '.self::MAKS_BULAN.' bulan sekali terbit.',
            ]);
        }

        $dibuat = 0;
        $sudahAda = 0;
        $kosong = 0;

        DB::transaction(function () use ($validated, $awal, $jumlahBulan, &$dibuat, &$sudahAda, &$kosong) {
            for ($i = 0; $i < $jumlahBulan; $i++) {
                $bulan = $awal->copy()->addMonthsNoOverflow($i);
                $periode = $bulan->format('Y-m');

                $ada = Invoice::query()
                    ->where('tenant_id', $validated['tenant_id'])
                    ->where('periode', $periode)
                    ->exists();

                if ($ada) {
                    $sudahAda++;

                    continue;
                }

                $ringkasan = TagihanRuang::ringkasan((int) $validated['tenant_id'], $periode);

                if ($ringkasan['total'] <= 0) {
                    $kosong++;

                    continue;
                }

                Invoice::create([
                    'nomor_tagihan' => Invoice::nextKodeTagihan(),
                    'tenant_id' => $validated['tenant_id'],
                    'periode' => $periode,
                    'jumlah' => $ringkasan['total'],
                    'jatuh_tempo' => $bulan->copy()->endOfMonth()->toDateString(),
                    'status' => 'Belum Dibayar',
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                $dibuat++;
            }
        });

        $pesan = "{$dibuat} tagihan diterbitkan.";

        if ($sudahAda > 0) {
            $pesan .= " {$sudahAda} bulan dilewati karena sudah ada tagihan.";
        }

        if ($kosong > 0) {
            $pesan .= " {$kosong} bulan dilewati karena tidak ada pemesanan.";
        }

        return redirect()->route('tagihan-pembayaran.index')->with('status', $pesan);
    }

    /**
     * Periode harus berformat YYYY-MM.
     */
    private static function periodeValid(string $periode): bool
    {
        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode) === 1;
    }

    /**
     * Simpan tagihan baru.
     */
    public function store(InvoiceRequest $request): RedirectResponse
    {
        $invoice = Invoice::create($this->normalizePayment($request->validated()));

        return redirect()
            ->route('tagihan-pembayaran.index')
            ->with('status', "Tagihan {$invoice->nomor_tagihan} berhasil diterbitkan.");
    }

    /**
     * Tampilkan formulir ubah tagihan.
     */
    public function edit(Invoice $invoice): View
    {
        return view('invoices.edit', [
            'invoice' => $invoice,
            'tenants' => $this->tenantOptions(),
        ]);
    }

    /**
     * Perbarui data tagihan.
     */
    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update($this->normalizePayment($request->validated()));

        return redirect()
            ->route('tagihan-pembayaran.index')
            ->with('status', "Tagihan {$invoice->nomor_tagihan} berhasil diperbarui.");
    }

    /**
     * Tandai tagihan sebagai lunas dengan satu klik.
     */
    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $invoice->update([
            'status' => 'Lunas',
            'tanggal_bayar' => now()->toDateString(),
        ]);

        return redirect()
            ->route('tagihan-pembayaran.index')
            ->with('status', "Tagihan {$invoice->nomor_tagihan} ditandai lunas.");
    }

    /**
     * Hapus tagihan.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $nomor = $invoice->nomor_tagihan;

        $invoice->delete();

        return redirect()
            ->route('tagihan-pembayaran.index')
            ->with('status', "Tagihan {$nomor} berhasil dihapus.");
    }

    /**
     * Jaga agar status dan tanggal bayar tetap konsisten.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePayment(array $data): array
    {
        if ($data['status'] === 'Lunas') {
            $data['tanggal_bayar'] ??= now()->toDateString();
        } else {
            // Tagihan yang belum lunas tidak boleh menyimpan tanggal pembayaran.
            $data['tanggal_bayar'] = null;
        }

        return $data;
    }

    /**
     * Pilihan tenant untuk dropdown, ber-key id tenant.
     *
     * @return Collection<int, string>
     */
    private function tenantOptions()
    {
        return Tenant::query()
            ->orderBy('nama_perusahaan')
            ->get()
            ->mapWithKeys(fn (Tenant $tenant) => [
                $tenant->id => "{$tenant->kode_tenant} — {$tenant->nama_perusahaan}",
            ]);
    }
}
