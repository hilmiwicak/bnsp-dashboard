<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenyewaRequest;
use App\Models\Penyewa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenyewaController extends Controller
{
    /**
     * Tampilkan daftar penyewa beserta pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'paket_layanan' => $this->allowedFilter($request->query('paket_layanan'), Penyewa::PAKET_LAYANAN),
            'status' => $this->allowedFilter($request->query('status'), Penyewa::STATUS),
        ];

        $penyewa = Penyewa::query()
            ->search($filters['q'])
            ->when($filters['paket_layanan'], fn ($query, $paket) => $query->where('paket_layanan', $paket))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        return view('penyewa.index', [
            'penyewa' => $penyewa,
            'filters' => $filters,
            'totalSemua' => Penyewa::count(),
        ]);
    }

    /**
     * Tampilkan formulir tambah penyewa.
     */
    public function create(): View
    {
        return view('penyewa.create', [
            'penyewa' => new Penyewa(['status' => 'Menunggu Verifikasi', 'durasi_sewa' => 12]),
        ]);
    }

    /**
     * Simpan data penyewa baru.
     */
    public function store(PenyewaRequest $request): RedirectResponse
    {
        $penyewa = Penyewa::create($request->validated());

        return redirect()
            ->route('penyewa.index')
            ->with('status', "Data penyewa {$penyewa->nama_pic} berhasil ditambahkan.");
    }

    /**
     * Tampilkan formulir ubah data penyewa.
     */
    public function edit(Penyewa $penyewa): View
    {
        return view('penyewa.edit', compact('penyewa'));
    }

    /**
     * Perbarui data penyewa.
     */
    public function update(PenyewaRequest $request, Penyewa $penyewa): RedirectResponse
    {
        $penyewa->update($request->validated());

        return redirect()
            ->route('penyewa.index')
            ->with('status', "Data penyewa {$penyewa->nama_pic} berhasil diperbarui.");
    }

    /**
     * Hapus data penyewa.
     */
    public function destroy(Penyewa $penyewa): RedirectResponse
    {
        $nama = $penyewa->nama_pic;

        $penyewa->delete();

        return redirect()
            ->route('penyewa.index')
            ->with('status', "Data penyewa {$nama} berhasil dihapus.");
    }

    /**
     * Only accept filter values that exist in the allowed list.
     *
     * @param  list<string>  $allowed
     */
    private function allowedFilter(mixed $value, array $allowed): ?string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : null;
    }
}
