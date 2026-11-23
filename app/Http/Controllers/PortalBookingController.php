<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortalBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalBookingController extends Controller
{
    /**
     * Formulir permintaan pemesanan ruang untuk tenant.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $tenant = $request->user()->tenant;

        // Ruang baru bisa dipesan setelah akun terhubung ke data sewa.
        if (! $tenant) {
            return redirect()
                ->route('portal.apply')
                ->with('status', 'Lengkapi pengajuan sewa terlebih dahulu sebelum memesan ruang.');
        }

        return view('portal.ruang', [
            'tenant' => $tenant,
            'rooms' => Room::query()
                ->where('status', 'Tersedia')
                ->orderBy('nama_ruang')
                ->get(),
            'bookings' => $tenant->bookings()
                ->with('room')
                ->orderByDesc('tanggal')
                ->orderByDesc('jam_mulai')
                ->limit(10)
                ->get(),
        ]);
    }

    /**
     * Simpan permintaan pemesanan sebagai status menunggu persetujuan admin.
     */
    public function store(PortalBookingRequest $request): RedirectResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return redirect()->route('portal.apply');
        }

        $validated = $request->validated();

        // tenant_id dan status selalu ditentukan server, bukan dari input.
        $booking = Booking::create([
            ...$validated,
            'kode_pemesanan' => Booking::nextKodePemesanan(),
            'harga_per_jam' => Room::query()
                ->whereKey($validated['room_id'])
                ->value('harga_per_jam'),
            'tenant_id' => $tenant->id,
            'status' => 'Menunggu',
        ]);

        return redirect()
            ->route('portal.ruang')
            ->with('status', "Permintaan pemesanan {$booking->kode_pemesanan} terkirim dan menunggu persetujuan admin.");
    }
}
