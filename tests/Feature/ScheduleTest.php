<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_schedule_page_is_only_reachable_by_admins(): void
    {
        $this->get('/jadwal-ruang')->assertRedirect('/login');

        $this->actingAs(User::factory()->tenant()->create())
            ->get('/jadwal-ruang')
            ->assertForbidden();
    }

    public function test_the_schedule_page_renders_for_admins(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/jadwal-ruang')
            ->assertOk()
            ->assertSee('Jadwal Penggunaan Ruang')
            ->assertSee('Ruang Terpakai');
    }

    public function test_only_approved_and_finished_bookings_appear_on_the_schedule(): void
    {
        $tenant = Tenant::factory()->create(['nama_perusahaan' => 'PT Nusantara Digital']);
        $room = Room::factory()->create(['nama_ruang' => 'Anggrek 1']);

        $tanggal = now()->addDays(3)->toDateString();

        Booking::factory()->create([
            'kode_pemesanan' => 'PMN-801',
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'tanggal' => $tanggal,
            'status' => 'Disetujui',
        ]);

        Booking::factory()->create([
            'kode_pemesanan' => 'PMN-802',
            'tanggal' => $tanggal,
            'status' => 'Selesai',
        ]);

        Booking::factory()->create([
            'kode_pemesanan' => 'PMN-803',
            'tanggal' => $tanggal,
            'status' => 'Menunggu',
        ]);

        Booking::factory()->create([
            'kode_pemesanan' => 'PMN-804',
            'tanggal' => $tanggal,
            'status' => 'Ditolak',
        ]);

        $response = $this->actingAs(User::factory()->create())->get('/jadwal-ruang');

        $response->assertOk();
        $response->assertSee('PT Nusantara Digital');
        $response->assertSee('Anggrek 1');
        $response->assertDontSee('PMN-803');
        $response->assertDontSee('PMN-804');
    }

    public function test_the_schedule_can_be_filtered_by_room(): void
    {
        $room = Room::factory()->create(['nama_ruang' => 'Anggrek 1']);
        $other = Room::factory()->create(['nama_ruang' => 'Melati 2']);

        $tanggal = now()->addDays(4)->toDateString();

        Booking::factory()->disetujui()->create([
            'tenant_id' => Tenant::factory()->create(['nama_perusahaan' => 'PT Anggrek Nusantara'])->id,
            'room_id' => $room->id,
            'tanggal' => $tanggal,
        ]);

        Booking::factory()->disetujui()->create([
            'tenant_id' => Tenant::factory()->create(['nama_perusahaan' => 'PT Melati Sejahtera'])->id,
            'room_id' => $other->id,
            'tanggal' => $tanggal,
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get("/jadwal-ruang?room={$room->id}");

        $response->assertOk();
        $response->assertSee('PT Anggrek Nusantara');
        $response->assertDontSee('PT Melati Sejahtera');
    }

    public function test_the_schedule_can_be_filtered_by_date(): void
    {
        $room = Room::factory()->create(['nama_ruang' => 'Anggrek 1']);

        $besok = now()->addDay()->toDateString();
        $mingguDepan = now()->addWeek()->toDateString();

        Booking::factory()->disetujui()->create([
            'room_id' => $room->id,
            'tanggal' => $besok,
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
        ]);

        Booking::factory()->disetujui()->create([
            'room_id' => $room->id,
            'tanggal' => $mingguDepan,
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get("/jadwal-ruang?tanggal={$besok}");

        $response->assertOk();
        $response->assertSee('09:00');
        $response->assertDontSee('13:00');
    }

    public function test_the_default_view_skips_bookings_in_the_past(): void
    {
        $lalu = Booking::factory()->disetujui()->create([
            'tanggal' => now()->subDays(5)->toDateString(),
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:00',
        ]);

        Booking::factory()->disetujui()->create([
            'tanggal' => now()->addDays(2)->toDateString(),
            'jam_mulai' => '14:00',
            'jam_selesai' => '15:00',
        ]);

        $response = $this->actingAs(User::factory()->create())->get('/jadwal-ruang');

        $response->assertOk();
        $response->assertDontSee($lalu->tenant->nama_perusahaan);
        $response->assertSee('14:00');
    }

    public function test_an_unknown_room_filter_is_ignored(): void
    {
        Booking::factory()->disetujui()->create([
            'tanggal' => now()->addDays(2)->toDateString(),
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/jadwal-ruang?room=999999')
            ->assertOk()
            ->assertSee('Jadwal Penggunaan Ruang');
    }

    public function test_the_empty_state_is_shown_when_nothing_is_scheduled(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/jadwal-ruang')
            ->assertOk()
            ->assertSee('Belum ada jadwal penggunaan ruang.');
    }
}
