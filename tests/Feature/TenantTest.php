<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_pages_require_authentication(): void
    {
        $this->get('/tenants')->assertRedirect('/login');
        $this->get('/tenants/create')->assertRedirect('/login');
    }

    public function test_index_lists_tenant_records(): void
    {
        $tenant = Tenant::factory()->create([
            'nama_pic' => 'Budi Santoso',
            'nama_perusahaan' => 'PT Nusantara Digital',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/tenants')
            ->assertOk()
            ->assertSee('Daftar Tenant')
            ->assertSee('Budi Santoso')
            ->assertSee($tenant->kode_tenant);
    }

    public function test_index_can_be_searched_by_name_or_code(): void
    {
        Tenant::factory()->create(['nama_pic' => 'Budi Santoso', 'kode_tenant' => 'TNT-AAA']);
        Tenant::factory()->create(['nama_pic' => 'Siti Rahmawati', 'kode_tenant' => 'TNT-BBB']);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/tenants?q=Budi')
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Rahmawati');

        $this->actingAs($user)
            ->get('/tenants?q=TNT-BBB')
            ->assertOk()
            ->assertSee('Siti Rahmawati')
            ->assertDontSee('Budi Santoso');
    }

    public function test_index_can_be_filtered_by_status(): void
    {
        Tenant::factory()->create(['nama_pic' => 'Budi Santoso', 'status' => 'Aktif']);
        Tenant::factory()->create(['nama_pic' => 'Siti Rahmawati', 'status' => 'Nonaktif']);

        $this->actingAs(User::factory()->create())
            ->get('/tenants?status=Nonaktif')
            ->assertOk()
            ->assertSee('Siti Rahmawati')
            ->assertDontSee('Budi Santoso');
    }

    public function test_create_and_edit_screens_are_reachable(): void
    {
        $tenant = Tenant::factory()->create(['nama_pic' => 'Budi Santoso']);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/tenants/create')
            ->assertOk()
            ->assertSee('Informasi PIC')
            ->assertSee('Detail Sewa');

        $this->actingAs($user)
            ->get("/tenants/{$tenant->id}/edit")
            ->assertOk()
            ->assertSee('Perbarui Data Tenant')
            ->assertSee('Budi Santoso');
    }

    public function test_a_tenant_can_be_stored(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tenants', $this->payload())
            ->assertRedirect('/tenants')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('tenants', [
            'kode_tenant' => 'TNT-100',
            'nama_pic' => 'Rina Wulandari',
            'email' => 'rina@ujicoba.id',
            'status' => 'Aktif',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tenants', [])
            ->assertSessionHasErrors([
                'kode_tenant',
                'nama_pic',
                'nama_perusahaan',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'alamat',
                'no_hp',
                'email',
                'tanggal_mulai_sewa',
                'durasi_sewa',
                'status',
            ]);

        $this->assertDatabaseCount('tenants', 0);
    }

    public function test_store_rejects_duplicate_code_and_email(): void
    {
        Tenant::factory()->create([
            'kode_tenant' => 'TNT-100',
            'email' => 'rina@ujicoba.id',
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/tenants', $this->payload())
            ->assertSessionHasErrors(['kode_tenant', 'email']);

        $this->assertDatabaseCount('tenants', 1);
    }

    public function test_validation_errors_are_displayed_on_the_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tenants', ['durasi_sewa' => 0]);

        $this->get('/tenants/create')
            ->assertOk()
            ->assertSee('The durasi sewa field must be at least 1.')
            ->assertSee('The kode tenant field is required.');
    }

    public function test_a_tenant_can_be_updated(): void
    {
        $tenant = Tenant::factory()->create(['nama_pic' => 'Budi Santoso']);

        $this->actingAs(User::factory()->create())
            ->put("/tenants/{$tenant->id}", $this->payload([
                'kode_tenant' => $tenant->kode_tenant,
                'email' => $tenant->email,
                'nama_pic' => 'Budi Santoso Wijaya',
                'durasi_sewa' => 24,
            ]))
            ->assertRedirect('/tenants')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'nama_pic' => 'Budi Santoso Wijaya',
            'durasi_sewa' => 24,
        ]);
    }

    public function test_a_tenant_can_be_deleted(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete("/tenants/{$tenant->id}")
            ->assertRedirect('/tenants')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'kode_tenant' => 'TNT-100',
            'nama_pic' => 'Rina Wulandari',
            'nama_perusahaan' => 'PT Uji Coba',
            'nib_npwp' => '1234567890123',
            'tempat_lahir' => 'Bogor',
            'tanggal_lahir' => '1992-05-05',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Uji No. 1, Bogor',
            'no_hp' => '081200011122',
            'email' => 'rina@ujicoba.id',
            'tanggal_mulai_sewa' => '2025-01-01',
            'durasi_sewa' => 12,
            'status' => 'Aktif',
        ], $overrides);
    }
}
