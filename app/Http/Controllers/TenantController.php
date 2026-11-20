<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantRequest;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Tampilkan daftar tenant beserta pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'status' => $this->allowedFilter($request->query('status'), Tenant::STATUS),
        ];

        $tenant = Tenant::query()
            ->search($filters['q'])
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        return view('tenants.index', [
            'tenant' => $tenant,
            'filters' => $filters,
            'totalSemua' => Tenant::count(),
        ]);
    }

    /**
     * Tampilkan formulir tambah tenant.
     */
    public function create(): View
    {
        return view('tenants.create', [
            'tenant' => new Tenant(['status' => 'Menunggu Verifikasi', 'durasi_sewa' => 12]),
        ]);
    }

    /**
     * Simpan data tenant baru.
     */
    public function store(TenantRequest $request): RedirectResponse
    {
        $tenant = Tenant::create($request->validated());

        return redirect()
            ->route('tenants.index')
            ->with('status', "Data tenant {$tenant->nama_pic} berhasil ditambahkan.");
    }

    /**
     * Tampilkan formulir ubah data tenant.
     */
    public function edit(Tenant $tenant): View
    {
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Perbarui data tenant.
     */
    public function update(TenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $tenant->update($request->validated());

        return redirect()
            ->route('tenants.index')
            ->with('status', "Data tenant {$tenant->nama_pic} berhasil diperbarui.");
    }

    /**
     * Hapus data tenant.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $nama = $tenant->nama_pic;

        $tenant->delete();

        return redirect()
            ->route('tenants.index')
            ->with('status', "Data tenant {$nama} berhasil dihapus.");
    }
}
