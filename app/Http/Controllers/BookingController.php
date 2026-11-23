<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Tampilkan daftar pemesanan beserta pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $roomFilter = $request->integer('room') ?: null;

        $filters = [
            'q' => trim((string) $request->query('q')),
            'status' => $this->allowedFilter($request->query('status'), Booking::STATUS),
            'room' => $roomFilter && Room::whereKey($roomFilter)->exists() ? $roomFilter : null,
        ];

        $bookings = Booking::query()
            ->with(['tenant', 'room'])
            ->search($filters['q'])
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['room'], fn ($query, $room) => $query->where('room_id', $room))
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->paginate(8)
            ->withQueryString();

        return view('bookings.index', [
            'bookings' => $bookings,
            'filters' => $filters,
            'totalSemua' => Booking::count(),
            'rooms' => $this->roomOptions(),
        ]);
    }

    /**
     * Tampilkan formulir tambah pemesanan.
     */
    public function create(): View
    {
        return view('bookings.create', [
            'booking' => new Booking([
                'kode_pemesanan' => Booking::nextKodePemesanan(),
                'tanggal' => now()->toDateString(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '10:00',
                'jumlah_peserta' => 4,
                'status' => 'Menunggu',
            ]),
            'tenants' => $this->tenantOptions(),
            'rooms' => $this->roomOptions(),
        ]);
    }

    /**
     * Simpan pemesanan baru.
     */
    public function store(BookingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Bekukan tarif ruang saat pemesanan dicatat.
        $validated['harga_per_jam'] = Room::query()
            ->whereKey($validated['room_id'])
            ->value('harga_per_jam');

        $booking = Booking::create($validated);

        return redirect()
            ->route('pemesanan.index')
            ->with('status', "Pemesanan {$booking->kode_pemesanan} berhasil dicatat.");
    }

    /**
     * Tampilkan formulir ubah pemesanan.
     */
    public function edit(Booking $booking): View
    {
        return view('bookings.edit', [
            'booking' => $booking,
            'tenants' => $this->tenantOptions(),
            'rooms' => $this->roomOptions(),
        ]);
    }

    /**
     * Perbarui data pemesanan.
     */
    public function update(BookingRequest $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validated();

        // Tarif mengikuti ruang baru bila ruangnya diganti.
        if ((int) $validated['room_id'] !== (int) $booking->room_id) {
            $validated['harga_per_jam'] = Room::query()
                ->whereKey($validated['room_id'])
                ->value('harga_per_jam');
        }

        $booking->update($validated);

        return redirect()
            ->route('pemesanan.index')
            ->with('status', "Pemesanan {$booking->kode_pemesanan} berhasil diperbarui.");
    }

    /**
     * Ubah status pemesanan dengan satu klik (mis. menyetujui permintaan).
     */
    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Booking::STATUS)],
        ]);

        $booking->update(['status' => $validated['status']]);

        return redirect()
            ->route('pemesanan.index')
            ->with('status', "Status pemesanan {$booking->kode_pemesanan} diubah menjadi {$booking->status}.");
    }

    /**
     * Hapus pemesanan.
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        $kode = $booking->kode_pemesanan;

        $booking->delete();

        return redirect()
            ->route('pemesanan.index')
            ->with('status', "Pemesanan {$kode} berhasil dihapus.");
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
     * Pilihan ruang untuk dropdown dan filter, ber-key id ruang.
     *
     * @return Collection<int, string>
     */
    private function roomOptions()
    {
        return Room::query()
            ->orderBy('nama_ruang')
            ->get()
            ->mapWithKeys(fn (Room $room) => [
                $room->id => "{$room->kode_ruang} — {$room->nama_ruang}",
            ]);
    }
}
