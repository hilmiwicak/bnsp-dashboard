<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Tampilkan daftar ruang beserta pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'tipe' => $this->allowedFilter($request->query('tipe'), Room::TIPE),
            'status' => $this->allowedFilter($request->query('status'), Room::STATUS),
        ];

        $rooms = Room::query()
            ->search($filters['q'])
            ->when($filters['tipe'], fn ($query, $tipe) => $query->where('tipe', $tipe))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->orderBy('kode_ruang')
            ->paginate(8)
            ->withQueryString();

        return view('rooms.index', [
            'rooms' => $rooms,
            'filters' => $filters,
            'totalSemua' => Room::count(),
        ]);
    }

    /**
     * Tampilkan formulir tambah ruang.
     */
    public function create(): View
    {
        return view('rooms.create', [
            'room' => new Room([
                'kode_ruang' => Room::nextKodeRuang(),
                'status' => 'Tersedia',
                'kapasitas' => 8,
                'harga_per_jam' => 100000,
            ]),
        ]);
    }

    /**
     * Simpan data ruang baru.
     */
    public function store(RoomRequest $request): RedirectResponse
    {
        $room = Room::create($request->validated());

        return redirect()
            ->route('ruang-fasilitas.index')
            ->with('status', "Ruang {$room->nama_ruang} berhasil ditambahkan.");
    }

    /**
     * Tampilkan formulir ubah data ruang.
     */
    public function edit(Room $room): View
    {
        return view('rooms.edit', compact('room'));
    }

    /**
     * Perbarui data ruang.
     */
    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()
            ->route('ruang-fasilitas.index')
            ->with('status', "Ruang {$room->nama_ruang} berhasil diperbarui.");
    }

    /**
     * Hapus data ruang.
     */
    public function destroy(Room $room): RedirectResponse
    {
        $nama = $room->nama_ruang;

        $room->delete();

        return redirect()
            ->route('ruang-fasilitas.index')
            ->with('status', "Ruang {$nama} berhasil dihapus.");
    }
}
