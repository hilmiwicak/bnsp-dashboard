<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Contract;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_tenant_can_open_the_room_page(): void
    {
        $room = Room::factory()->create([
            'nama_ruang' => 'Ruang Melati',
            'status' => 'Tersedia',
            'kapasitas' => 8,
        ]);

        $this->actingAs($this->tenantUser())
            ->get('/portal/ruang')
            ->assertOk()
            ->assertSee('Ruang Tersedia')
            ->assertSee('Ruang Melati')
            ->assertSee('Ajukan Pemesanan')
            ->assertSee('Permintaan Saya');
    }

    public function test_a_tenant_without_a_tenancy_is_sent_to_the_application_form(): void
    {
        $this->actingAs(User::factory()->tenant()->create())
            ->get('/portal/ruang')
            ->assertRedirect('/portal/pengajuan');
    }

    public function test_a_tenant_can_request_a_room(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room))
            ->assertRedirect('/portal/ruang')
            ->assertSessionHas('status');

        $booking = Booking::where('room_id', $room->id)->firstOrFail();

        $this->assertSame($tenant->id, $booking->tenant_id);
        $this->assertSame('Menunggu', $booking->status);
        $this->assertSame('PMN-001', $booking->kode_pemesanan);
    }

    public function test_the_tenant_id_cannot_be_spoofed(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();
        $other = Tenant::factory()->create();

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room) + ['tenant_id' => $other->id])
            ->assertRedirect('/portal/ruang');

        $this->assertSame($tenant->id, Booking::firstOrFail()->tenant_id);
    }

    public function test_the_request_is_always_pending_approval(): void
    {
        $room = $this->room();

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room) + ['status' => 'Disetujui'])
            ->assertRedirect('/portal/ruang');

        $this->assertSame('Menunggu', Booking::firstOrFail()->status);
    }

    public function test_an_overlapping_request_is_rejected(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'Menunggu',
        ]);

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room, [
                'jam_mulai' => '09:30',
                'jam_selesai' => '10:30',
            ]))
            ->assertSessionHasErrors(['jam_mulai']);

        $this->assertSame(1, Booking::count());
    }

    public function test_a_room_that_is_not_available_cannot_be_requested(): void
    {
        $room = $this->room(status: 'Perbaikan');

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room))
            ->assertSessionHasErrors(['room_id']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_the_date_cannot_be_in_the_past(): void
    {
        $room = $this->room();

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room, [
                'tanggal' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors(['tanggal']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_a_request_within_the_contract_period_is_accepted(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        $this->contractFor($tenant);

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room))
            ->assertRedirect('/portal/ruang')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_a_request_after_the_contract_ends_is_rejected(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        $this->contractFor($tenant, now()->subYear(), now()->subDay());

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room))
            ->assertSessionHasErrors(['tanggal']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_a_request_before_the_contract_starts_is_rejected(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        $this->contractFor($tenant, now()->addDays(10), now()->addYear());

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room))
            ->assertSessionHasErrors(['tanggal']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_a_tenant_whose_contract_ended_cannot_request_a_room(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        $this->contractFor($tenant, now()->subYear(), now()->subMonth(), 'Berakhir');

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room))
            ->assertSessionHasErrors(['tanggal']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_a_tenant_whose_contract_was_cancelled_cannot_request_a_room(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        $this->contractFor($tenant, now()->subMonth(), now()->addYear(), 'Dibatalkan');

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room))
            ->assertSessionHasErrors(['tanggal']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_a_renewed_tenant_can_still_request_a_room(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        // Kontrak lama sudah berakhir, lalu diperpanjang.
        $this->contractFor($tenant, now()->subYears(2), now()->subYear(), 'Berakhir');
        $this->contractFor($tenant);

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room))
            ->assertRedirect('/portal/ruang')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_a_request_above_the_room_capacity_is_rejected(): void
    {
        $room = $this->room(kapasitas: 4);

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room, ['jumlah_peserta' => 5]))
            ->assertSessionHasErrors(['jumlah_peserta']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_a_request_at_the_room_capacity_is_accepted(): void
    {
        $room = $this->room(kapasitas: 4);

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room, ['jumlah_peserta' => 4]))
            ->assertRedirect('/portal/ruang')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_a_tenant_without_a_contract_is_not_restricted(): void
    {
        $room = $this->room();
        $tenant = Tenant::factory()->create();

        $this->actingAs($this->tenantUser($tenant))
            ->post('/portal/ruang', $this->payload($room, [
                'tanggal' => now()->addDays(90)->toDateString(),
            ]))
            ->assertRedirect('/portal/ruang')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_the_room_rate_is_snapshotted_on_the_request(): void
    {
        $room = Room::factory()->create([
            'status' => 'Tersedia',
            'kapasitas' => 8,
            'harga_per_jam' => 175000,
        ]);

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room, ['jumlah_peserta' => 2]))
            ->assertRedirect('/portal/ruang');

        $this->assertSame(175000, Booking::firstOrFail()->harga_per_jam);
    }

    public function test_booking_times_must_be_in_half_hour_steps(): void
    {
        $room = $this->room();

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room, [
                'jam_mulai' => '10:01',
                'jam_selesai' => '11:00',
            ]))
            ->assertSessionHasErrors(['jam_mulai']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_the_end_time_must_also_be_in_half_hour_steps(): void
    {
        $room = $this->room();

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room, [
                'jam_mulai' => '10:00',
                'jam_selesai' => '10:31',
            ]))
            ->assertSessionHasErrors(['jam_selesai']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_half_hour_slots_are_accepted(): void
    {
        $room = $this->room();

        $this->actingAs($this->tenantUser())
            ->post('/portal/ruang', $this->payload($room, [
                'jam_mulai' => '10:30',
                'jam_selesai' => '11:30',
            ]))
            ->assertRedirect('/portal/ruang')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_the_page_only_lists_the_tenant_own_requests(): void
    {
        $tenant = Tenant::factory()->create();
        $other = Tenant::factory()->create();

        Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'kode_pemesanan' => 'PMN-MILIK',
        ]);

        Booking::factory()->create([
            'tenant_id' => $other->id,
            'kode_pemesanan' => 'PMN-LAIN',
        ]);

        $this->actingAs($this->tenantUser($tenant))
            ->get('/portal/ruang')
            ->assertOk()
            ->assertSee('PMN-MILIK')
            ->assertDontSee('PMN-LAIN');
    }

    public function test_the_portal_links_to_the_room_page(): void
    {
        $this->actingAs($this->tenantUser())
            ->get('/portal')
            ->assertOk()
            ->assertSee('Pesan Ruang')
            ->assertSee('Ruang &amp; Fasilitas', false);
    }

    public function test_admins_cannot_open_the_room_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/portal/ruang')
            ->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/portal/ruang')->assertRedirect('/login');
        $this->post('/portal/ruang', [])->assertRedirect('/login');
    }

    private function tenantUser(?Tenant $tenant = null): User
    {
        return User::factory()->tenant()->create([
            'tenant_id' => ($tenant ?? Tenant::factory()->create())->id,
        ]);
    }

    /**
     * Ruang uji dengan kapasitas tetap agar jumlah peserta selalu pasti.
     */
    private function room(int $kapasitas = 8, string $status = 'Tersedia'): Room
    {
        return Room::factory()->create([
            'kapasitas' => $kapasitas,
            'status' => $status,
        ]);
    }

    /**
     * Kontrak aktif yang berlaku sebagai periode sewa tenant.
     */
    private function contractFor(
        Tenant $tenant,
        $mulai = null,
        $berakhir = null,
        string $status = 'Aktif',
    ): Contract {
        return Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'tanggal_mulai' => ($mulai ?? now()->subMonth())->toDateString(),
            'tanggal_berakhir' => ($berakhir ?? now()->addYear())->toDateString(),
            'status' => $status,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(Room $room, array $overrides = []): array
    {
        return array_merge([
            'room_id' => $room->id,
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:00',
            'jumlah_peserta' => 4,
            'catatan' => 'Butuh proyektor.',
        ], $overrides);
    }
}
