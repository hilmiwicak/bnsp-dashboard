<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_pages_are_only_reachable_by_admins(): void
    {
        $this->get('/ruang-fasilitas')->assertRedirect('/login');

        $this->actingAs(User::factory()->tenant()->create())
            ->get('/ruang-fasilitas')
            ->assertForbidden();
    }

    public function test_index_lists_rooms(): void
    {
        $room = Room::factory()->create([
            'kode_ruang' => 'RNG-900',
            'nama_ruang' => 'Anggrek 1',
            'status' => 'Tersedia',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/ruang-fasilitas')
            ->assertOk()
            ->assertSee('Daftar Ruang & Fasilitas')
            ->assertSee($room->kode_ruang)
            ->assertSee('Anggrek 1');
    }

    public function test_index_can_be_searched_and_filtered(): void
    {
        Room::factory()->create([
            'kode_ruang' => 'RNG-901',
            'nama_ruang' => 'Anggrek 1',
            'tipe' => 'Meeting Room',
            'status' => 'Tersedia',
        ]);

        Room::factory()->create([
            'kode_ruang' => 'RNG-902',
            'nama_ruang' => 'Melati Suite',
            'tipe' => 'Private Office',
            'status' => 'Perbaikan',
        ]);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/ruang-fasilitas?q=Anggrek')
            ->assertOk()
            ->assertSee('Anggrek 1')
            ->assertDontSee('Melati Suite');

        $this->actingAs($admin)
            ->get('/ruang-fasilitas?tipe=Private+Office')
            ->assertOk()
            ->assertSee('Melati Suite')
            ->assertDontSee('Anggrek 1');

        $this->actingAs($admin)
            ->get('/ruang-fasilitas?status=Perbaikan')
            ->assertOk()
            ->assertSee('Melati Suite')
            ->assertDontSee('Anggrek 1');
    }

    public function test_create_and_edit_screens_are_reachable(): void
    {
        $room = Room::factory()->create(['nama_ruang' => 'Anggrek 1']);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/ruang-fasilitas/create')
            ->assertOk()
            ->assertSee('Informasi Ruang')
            ->assertSee('Fasilitas');

        $this->actingAs($admin)
            ->get("/ruang-fasilitas/{$room->id}/edit")
            ->assertOk()
            ->assertSee('Perbarui Data Ruang')
            ->assertSee('Anggrek 1');
    }

    public function test_a_room_can_be_stored(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/ruang-fasilitas', $this->payload())
            ->assertRedirect('/ruang-fasilitas')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('rooms', [
            'kode_ruang' => 'RNG-100',
            'nama_ruang' => 'Anggrek 1',
            'tipe' => 'Meeting Room',
            'harga_per_jam' => 150000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/ruang-fasilitas', [])
            ->assertSessionHasErrors([
                'kode_ruang',
                'nama_ruang',
                'tipe',
                'kapasitas',
                'lokasi',
                'harga_per_jam',
                'status',
            ]);

        $this->assertDatabaseCount('rooms', 0);
    }

    public function test_store_rejects_duplicate_code(): void
    {
        Room::factory()->create(['kode_ruang' => 'RNG-100']);

        $this->actingAs(User::factory()->create())
            ->post('/ruang-fasilitas', $this->payload())
            ->assertSessionHasErrors(['kode_ruang']);
    }

    public function test_a_room_can_be_updated(): void
    {
        $room = Room::factory()->create(['nama_ruang' => 'Anggrek 1']);

        $this->actingAs(User::factory()->create())
            ->put("/ruang-fasilitas/{$room->id}", $this->payload([
                'kode_ruang' => $room->kode_ruang,
                'nama_ruang' => 'Anggrek Utama',
                'kapasitas' => 20,
            ]))
            ->assertRedirect('/ruang-fasilitas')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'nama_ruang' => 'Anggrek Utama',
            'kapasitas' => 20,
        ]);
    }

    public function test_a_room_can_be_deleted(): void
    {
        $room = Room::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete("/ruang-fasilitas/{$room->id}")
            ->assertRedirect('/ruang-fasilitas')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    public function test_the_next_room_code_follows_the_sequence(): void
    {
        $this->assertSame('RNG-001', Room::nextKodeRuang());

        Room::factory()->create(['kode_ruang' => 'RNG-006']);

        $this->assertSame('RNG-007', Room::nextKodeRuang());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'kode_ruang' => 'RNG-100',
            'nama_ruang' => 'Anggrek 1',
            'tipe' => 'Meeting Room',
            'kapasitas' => 12,
            'lokasi' => 'Lantai 3',
            'harga_per_jam' => 150000,
            'fasilitas' => 'AC, Wi-Fi, Proyektor',
            'status' => 'Tersedia',
        ], $overrides);
    }
}
