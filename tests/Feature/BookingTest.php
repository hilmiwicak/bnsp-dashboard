<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_pages_are_only_reachable_by_admins(): void
    {
        $this->get('/pemesanan')->assertRedirect('/login');

        $this->actingAs(User::factory()->customer()->create())
            ->get('/pemesanan')
            ->assertForbidden();
    }

    public function test_index_lists_bookings(): void
    {
        $tenant = Tenant::factory()->create(['nama_perusahaan' => 'PT Nusantara Digital']);
        $room = Room::factory()->create(['nama_ruang' => 'Anggrek 1']);

        Booking::factory()->create([
            'kode_pemesanan' => 'PMN-900',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/pemesanan')
            ->assertOk()
            ->assertSee('Daftar Pemesanan')
            ->assertSee('PMN-900')
            ->assertSee('PT Nusantara Digital')
            ->assertSee('Anggrek 1');
    }

    public function test_index_can_be_searched_and_filtered(): void
    {
        $tenant = Tenant::factory()->create(['nama_perusahaan' => 'PT Nusantara Digital']);
        $room = Room::factory()->create(['nama_ruang' => 'Anggrek 1']);

        Booking::factory()->create([
            'kode_pemesanan' => 'PMN-901',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => 'Menunggu',
        ]);

        Booking::factory()->create(['kode_pemesanan' => 'PMN-902', 'status' => 'Selesai']);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/pemesanan?q=PMN-901')
            ->assertOk()
            ->assertSee('PMN-901')
            ->assertDontSee('PMN-902');

        $this->actingAs($admin)
            ->get('/pemesanan?status=Selesai')
            ->assertOk()
            ->assertSee('PMN-902')
            ->assertDontSee('PMN-901');

        $this->actingAs($admin)
            ->get("/pemesanan?room={$room->id}")
            ->assertOk()
            ->assertSee('PMN-901')
            ->assertDontSee('PMN-902');
    }

    public function test_create_and_edit_screens_are_reachable(): void
    {
        $booking = Booking::factory()->create();

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/pemesanan/create')
            ->assertOk()
            ->assertSee('Pemesan')
            ->assertSee('Jadwal Pemakaian')
            ->assertSee('Kode Pemesanan');

        $this->actingAs($admin)
            ->get("/pemesanan/{$booking->id}/edit")
            ->assertOk()
            ->assertSee('Perbarui Pemesanan')
            ->assertSee($booking->kode_pemesanan);
    }

    public function test_a_booking_can_be_stored(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/pemesanan', $this->payload([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
            ]))
            ->assertRedirect('/pemesanan')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('bookings', [
            'kode_pemesanan' => 'PMN-100',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => 'Disetujui',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/pemesanan', [])
            ->assertSessionHasErrors([
                'kode_pemesanan',
                'tenant_id',
                'room_id',
                'tanggal',
                'jam_mulai',
                'jam_selesai',
                'jumlah_peserta',
                'status',
            ]);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_the_end_time_must_be_after_the_start_time(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/pemesanan', $this->payload([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'jam_mulai' => '11:00',
                'jam_selesai' => '10:00',
            ]))
            ->assertSessionHasErrors(['jam_selesai']);
    }

    public function test_an_overlapping_booking_on_the_same_room_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create();

        Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'tanggal' => '2026-12-01',
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'status' => 'Disetujui',
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/pemesanan', $this->payload([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'tanggal' => '2026-12-01',
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
            ]))
            ->assertSessionHasErrors(['jam_mulai']);

        $this->assertSame(1, Booking::count());
    }

    public function test_back_to_back_bookings_on_the_same_room_are_allowed(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create();

        Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'tanggal' => '2026-12-01',
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'status' => 'Disetujui',
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/pemesanan', $this->payload([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'tanggal' => '2026-12-01',
                'jam_mulai' => '11:00',
                'jam_selesai' => '12:00',
            ]))
            ->assertRedirect('/pemesanan');

        $this->assertSame(2, Booking::count());
    }

    public function test_a_booking_can_be_updated_without_conflicting_with_itself(): void
    {
        $booking = Booking::factory()->create([
            'tanggal' => '2026-12-02',
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'Disetujui',
        ]);

        $this->actingAs(User::factory()->create())
            ->put("/pemesanan/{$booking->id}", $this->payload([
                'kode_pemesanan' => $booking->kode_pemesanan,
                'tenant_id' => $booking->tenant_id,
                'room_id' => $booking->room_id,
                'tanggal' => '2026-12-02',
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
            ]))
            ->assertRedirect('/pemesanan')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
        $this->assertSame('11:00', $booking->fresh()->jamSelesaiShort());
    }

    public function test_the_status_can_be_changed_with_one_click(): void
    {
        $booking = Booking::factory()->create(['status' => 'Menunggu']);

        $this->actingAs(User::factory()->create())
            ->patch("/pemesanan/{$booking->id}/status", ['status' => 'Disetujui'])
            ->assertRedirect('/pemesanan')
            ->assertSessionHas('status');

        $this->assertSame('Disetujui', $booking->fresh()->status);
    }

    public function test_an_unknown_status_is_rejected(): void
    {
        $booking = Booking::factory()->create(['status' => 'Menunggu']);

        $this->actingAs(User::factory()->create())
            ->patch("/pemesanan/{$booking->id}/status", ['status' => 'Entah'])
            ->assertSessionHasErrors(['status']);

        $this->assertSame('Menunggu', $booking->fresh()->status);
    }

    public function test_a_booking_can_be_deleted(): void
    {
        $booking = Booking::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete("/pemesanan/{$booking->id}")
            ->assertRedirect('/pemesanan')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    public function test_the_next_booking_code_follows_the_sequence(): void
    {
        $this->assertSame('PMN-001', Booking::nextKodePemesanan());

        Booking::factory()->create(['kode_pemesanan' => 'PMN-042']);

        $this->assertSame('PMN-043', Booking::nextKodePemesanan());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'kode_pemesanan' => 'PMN-100',
            'tenant_id' => Tenant::factory()->create()->id,
            'room_id' => Room::factory()->create()->id,
            'tanggal' => '2026-12-05',
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'jumlah_peserta' => 6,
            'status' => 'Disetujui',
            'catatan' => 'Rapat internal.',
        ], $overrides);
    }
}
