<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractRequest;
use App\Models\Contract;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ContractController extends Controller
{
    /**
     * Tampilkan daftar kontrak sewa beserta pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'status' => $this->allowedFilter($request->query('status'), Contract::STATUS),
        ];

        $contracts = Contract::query()
            ->with('tenant')
            ->search($filters['q'])
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('tanggal_mulai')
            ->orderBy('nomor_kontrak')
            ->paginate(8)
            ->withQueryString();

        return view('contracts.index', [
            'contracts' => $contracts,
            'filters' => $filters,
            'totalSemua' => Contract::count(),
        ]);
    }

    /**
     * Tampilkan formulir tambah kontrak.
     */
    public function create(): View
    {
        return view('contracts.create', [
            'contract' => new Contract([
                'nomor_kontrak' => Contract::nextKodeKontrak(),
                'tanggal_mulai' => now(),
                'tanggal_berakhir' => now()->addYear(),
                'nilai_sewa' => 12000000,
                'status' => 'Aktif',
            ]),
            'tenants' => $this->tenantOptions(),
            'tenantStarts' => $this->tenantStartDates(),
        ]);
    }

    /**
     * Simpan kontrak baru.
     */
    public function store(ContractRequest $request): RedirectResponse
    {
        $contract = Contract::create($request->validated());

        return redirect()
            ->route('kontrak-sewa.index')
            ->with('status', "Kontrak {$contract->nomor_kontrak} berhasil dicatat.");
    }

    /**
     * Tampilkan formulir ubah kontrak.
     */
    public function edit(Contract $contract): View
    {
        return view('contracts.edit', [
            'contract' => $contract,
            'tenants' => $this->tenantOptions(),
            'tenantStarts' => $this->tenantStartDates(),
        ]);
    }

    /**
     * Perbarui data kontrak.
     */
    public function update(ContractRequest $request, Contract $contract): RedirectResponse
    {
        $contract->update($request->validated());

        return redirect()
            ->route('kontrak-sewa.index')
            ->with('status', "Kontrak {$contract->nomor_kontrak} berhasil diperbarui.");
    }

    /**
     * Hapus kontrak.
     */
    public function destroy(Contract $contract): RedirectResponse
    {
        $nomor = $contract->nomor_kontrak;

        $contract->delete();

        return redirect()
            ->route('kontrak-sewa.index')
            ->with('status', "Kontrak {$nomor} berhasil dihapus.");
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

    /**
     * Saran tanggal mulai kontrak baru: sehari setelah kontrak terakhir tenant.
     * Dipakai untuk mengisi otomatis tanggal mulai saat tenant dipilih.
     *
     * @return array<int, string>
     */
    private function tenantStartDates(): array
    {
        return Contract::query()
            ->whereIn('status', ['Aktif', 'Berakhir'])
            ->selectRaw('tenant_id, MAX(tanggal_berakhir) as berakhir')
            ->groupBy('tenant_id')
            ->get()
            ->mapWithKeys(fn (Contract $contract) => [
                $contract->tenant_id => Carbon::parse($contract->berakhir)->addDay()->format('Y-m-d'),
            ])
            ->all();
    }
}
