<?php

namespace Tests\Feature;

use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenyewaTest extends TestCase
{
    use RefreshDatabase;

    public function test_penyewa_pages_require_authentication(): void
    {
        $this->get('/penyewa')->assertRedirect('/login');
        $this->get('/penyewa/create')->assertRedirect('/login');
    }

    public function test_index_lists_penyewa_records(): void
    {
        $penyewa = Penyewa::factory()->create([
            'nama_pic' => 'Budi Santoso',
            'nama_perusahaan' => 'PT Nusantara Digital',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/penyewa')
            ->assertOk()
            ->assertSee('Daftar Penyewa')
            ->assertSee('Budi Santoso')
            ->assertSee($penyewa->kode_penyewa);
    }

    public function test_index_can_be_searched_by_name_or_code(): void
    {
        Penyewa::factory()->create(['nama_pic' => 'Budi Santoso', 'kode_penyewa' => 'PYW-AAA']);
        Penyewa::factory()->create(['nama_pic' => 'Siti Rahmawati', 'kode_penyewa' => 'PYW-BBB']);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/penyewa?q=Budi')
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Rahmawati');

        $this->actingAs($user)
            ->get('/penyewa?q=PYW-BBB')
            ->assertOk()
            ->assertSee('Siti Rahmawati')
            ->assertDontSee('Budi Santoso');
    }

    public function test_index_can_be_filtered_by_status(): void
    {
        Penyewa::factory()->create(['nama_pic' => 'Budi Santoso', 'status' => 'Aktif']);
        Penyewa::factory()->create(['nama_pic' => 'Siti Rahmawati', 'status' => 'Nonaktif']);

        $this->actingAs(User::factory()->create())
            ->get('/penyewa?status=Nonaktif')
            ->assertOk()
            ->assertSee('Siti Rahmawati')
            ->assertDontSee('Budi Santoso');
    }

    public function test_create_and_edit_screens_are_reachable(): void
    {
        $penyewa = Penyewa::factory()->create(['nama_pic' => 'Budi Santoso']);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/penyewa/create')
            ->assertOk()
            ->assertSee('Informasi PIC')
            ->assertSee('Detail Sewa');

        $this->actingAs($user)
            ->get("/penyewa/{$penyewa->id}/edit")
            ->assertOk()
            ->assertSee('Perbarui Data Penyewa')
            ->assertSee('Budi Santoso');
    }

    public function test_a_penyewa_can_be_stored(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/penyewa', $this->payload())
            ->assertRedirect('/penyewa')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('penyewa', [
            'kode_penyewa' => 'PYW-100',
            'nama_pic' => 'Rina Wulandari',
            'email' => 'rina@ujicoba.id',
            'status' => 'Aktif',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/penyewa', [])
            ->assertSessionHasErrors([
                'kode_penyewa',
                'nama_pic',
                'nama_perusahaan',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'alamat',
                'no_hp',
                'email',
                'paket_layanan',
                'tanggal_mulai_sewa',
                'durasi_sewa',
                'status',
            ]);

        $this->assertDatabaseCount('penyewa', 0);
    }

    public function test_store_rejects_duplicate_code_and_email(): void
    {
        Penyewa::factory()->create([
            'kode_penyewa' => 'PYW-100',
            'email' => 'rina@ujicoba.id',
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/penyewa', $this->payload())
            ->assertSessionHasErrors(['kode_penyewa', 'email']);

        $this->assertDatabaseCount('penyewa', 1);
    }

    public function test_validation_errors_are_displayed_on_the_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/penyewa', ['durasi_sewa' => 0]);

        $this->get('/penyewa/create')
            ->assertOk()
            ->assertSee('The durasi sewa field must be at least 1.')
            ->assertSee('The kode penyewa field is required.');
    }

    public function test_a_penyewa_can_be_updated(): void
    {
        $penyewa = Penyewa::factory()->create(['nama_pic' => 'Budi Santoso']);

        $this->actingAs(User::factory()->create())
            ->put("/penyewa/{$penyewa->id}", $this->payload([
                'kode_penyewa' => $penyewa->kode_penyewa,
                'email' => $penyewa->email,
                'nama_pic' => 'Budi Santoso Wijaya',
                'durasi_sewa' => 24,
            ]))
            ->assertRedirect('/penyewa')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('penyewa', [
            'id' => $penyewa->id,
            'nama_pic' => 'Budi Santoso Wijaya',
            'durasi_sewa' => 24,
        ]);
    }

    public function test_a_penyewa_can_be_deleted(): void
    {
        $penyewa = Penyewa::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete("/penyewa/{$penyewa->id}")
            ->assertRedirect('/penyewa')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('penyewa', ['id' => $penyewa->id]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'kode_penyewa' => 'PYW-100',
            'nama_pic' => 'Rina Wulandari',
            'nama_perusahaan' => 'PT Uji Coba',
            'nib_npwp' => '1234567890123',
            'tempat_lahir' => 'Bogor',
            'tanggal_lahir' => '1992-05-05',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Uji No. 1, Bogor',
            'no_hp' => '081200011122',
            'email' => 'rina@ujicoba.id',
            'paket_layanan' => 'Virtual Office',
            'tanggal_mulai_sewa' => '2025-01-01',
            'durasi_sewa' => 12,
            'status' => 'Aktif',
        ], $overrides);
    }
}
