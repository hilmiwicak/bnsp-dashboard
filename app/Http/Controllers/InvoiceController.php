<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Support\TagihanRuang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class InvoiceController extends Controller
{
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
        $periodeValid = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode) === 1;

        $siapHitung = $tenantId !== null && $periodeValid;

        $saran = $siapHitung ? TagihanRuang::ringkasan($tenantId, $periode) : null;

        $duplikat = $siapHitung
            ? Invoice::query()->where('tenant_id', $tenantId)->where('periode', $periode)->count()
            : 0;

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
            'filter' => [
                'tenant_id' => $tenantId,
                'periode' => $periodeValid ? $periode : '',
            ],
        ]);
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
