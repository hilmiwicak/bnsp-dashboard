<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Jadwal pemakaian ruang, diturunkan dari pemesanan yang sudah disetujui.
     *
     * Modul ini sengaja hanya-baca: penjadwalan dibuat dan diubah lewat
     * modul Pemesanan agar tidak ada dua sumber data yang bisa berbeda.
     */
    public function index(Request $request): View
    {
        $roomFilter = $request->integer('room') ?: null;

        $filters = [
            'room' => $roomFilter && Room::whereKey($roomFilter)->exists() ? $roomFilter : null,
            'tanggal' => $this->validDate($request->query('tanggal')),
        ];

        $bookings = Booking::query()
            ->with(['tenant', 'room'])
            ->whereIn('status', Booking::STATUS_JADWAL)
            ->when($filters['room'], fn ($query, $room) => $query->where('room_id', $room))
            ->when(
                $filters['tanggal'],
                fn ($query, $tanggal) => $query->whereDate('tanggal', $tanggal),
                fn ($query) => $query->whereDate('tanggal', '>=', now()->toDateString()),
            )
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        return view('schedules.index', [
            'jadwal' => $bookings->groupBy(fn (Booking $booking) => $booking->tanggal->toDateString()),
            'filters' => $filters,
            'rooms' => Room::query()
                ->orderBy('nama_ruang')
                ->get()
                ->mapWithKeys(fn (Room $room) => [$room->id => "{$room->kode_ruang} — {$room->nama_ruang}"]),
            'totalJadwal' => $bookings->count(),
            'totalRuang' => $bookings->pluck('room_id')->unique()->count(),
        ]);
    }

    /**
     * Terima hanya tanggal yang benar-benar bisa diurai.
     */
    private function validDate(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
