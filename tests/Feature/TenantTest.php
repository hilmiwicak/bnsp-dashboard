<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Contract;
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
            ->assertSee('Status Keanggotaan')
            ->assertSee('value="'.Tenant::nextKodeTenant().'"', false);

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

    public function test_creating_a_tenant_provisions_a_login_account(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tenants', $this->payload())
            ->assertRedirect('/tenants')
            ->assertSessionHas('kredensial');

        $tenant = Tenant::where('kode_tenant', 'TNT-100')->firstOrFail();
        $account = User::where('email', 'rina@ujicoba.id')->firstOrFail();

        $this->assertSame(UserRole::Tenant, $account->role);
        $this->assertSame($tenant->id, $account->tenant_id);
        $this->assertSame('Rina Wulandari', $account->name);
    }

    public function test_the_generated_password_is_hashed_and_usable_for_login(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post('/tenants', $this->payload());

        $password = $response->getSession()->get('kredensial')['password'];
        $account = User::where('email', 'rina@ujicoba.id')->firstOrFail();

        $this->assertNotSame($password, $account->password);
        $this->assertTrue(password_verify($password, $account->password));

        $this->post('/logout');

        $this->post('/login', [
            'email' => 'rina@ujicoba.id',
            'password' => $password,
        ])->assertRedirect('/portal');

        $this->assertAuthenticatedAs($account);
    }

    public function test_the_generated_password_is_shown_once(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post('/tenants', $this->payload());

        $password = $response->getSession()->get('kredensial')['password'];

        // Tampil pada permintaan pertama setelah redirect.
        $this->get('/tenants')
            ->assertOk()
            ->assertSee('Akun login tenant dibuat')
            ->assertSee('Password sementara')
            ->assertSee('rina@ujicoba.id')
            ->assertSee('type="password"', false)
            ->assertSee($password);

        // Hilang setelah dibaca sekali.
        $this->get('/tenants')
            ->assertOk()
            ->assertDontSee('Password sementara')
            ->assertDontSee($password);
    }

    public function test_creating_a_tenant_links_an_existing_unlinked_account(): void
    {
        $account = User::factory()->tenant()->create([
            'name' => 'Nama Lama',
            'email' => 'rina@ujicoba.id',
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/tenants', $this->payload())
            ->assertRedirect('/tenants')
            ->assertSessionMissing('kredensial');

        $tenant = Tenant::where('kode_tenant', 'TNT-100')->firstOrFail();

        $this->assertSame($tenant->id, $account->fresh()->tenant_id);
        $this->assertSame('Rina Wulandari', $account->fresh()->name);
        $this->assertSame(1, User::where('email', 'rina@ujicoba.id')->count());
    }

    public function test_store_rejects_an_email_linked_to_another_tenant(): void
    {
        $other = Tenant::factory()->create();

        User::factory()->tenant()->create([
            'email' => 'rina@ujicoba.id',
            'tenant_id' => $other->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/tenants', $this->payload())
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('tenants', ['kode_tenant' => 'TNT-100']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tenants', [])
            ->assertSessionHasErrors([
                'kode_tenant',
                'nama_pic',
                'nama_perusahaan',
                'alamat',
                'no_hp',
                'email',
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
            ->post('/tenants', []);

        $this->get('/tenants/create')
            ->assertOk()
            ->assertSee('The kode tenant field is required.')
            ->assertSee('The nama lengkap PIC field is required.');
    }

    public function test_a_tenant_can_be_updated(): void
    {
        $tenant = Tenant::factory()->create(['nama_pic' => 'Budi Santoso']);

        $this->actingAs(User::factory()->create())
            ->put("/tenants/{$tenant->id}", $this->payload([
                'kode_tenant' => $tenant->kode_tenant,
                'email' => $tenant->email,
                'nama_pic' => 'Budi Santoso Wijaya',
            ]))
            ->assertRedirect('/tenants')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'nama_pic' => 'Budi Santoso Wijaya',
        ]);
    }

    public function test_updating_a_tenant_syncs_its_login_account(): void
    {
        $tenant = Tenant::factory()->create([
            'nama_pic' => 'Budi Santoso',
            'email' => 'budi@ujicoba.id',
        ]);

        $account = User::factory()->tenant()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@ujicoba.id',
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->put("/tenants/{$tenant->id}", $this->payload([
                'kode_tenant' => $tenant->kode_tenant,
                'nama_pic' => 'Budi Santoso Wijaya',
                'email' => 'budi.baru@ujicoba.id',
            ]))
            ->assertRedirect('/tenants');

        $this->assertSame('Budi Santoso Wijaya', $account->fresh()->name);
        $this->assertSame('budi.baru@ujicoba.id', $account->fresh()->email);
    }

    public function test_updating_a_tenant_leaves_other_pic_accounts_alone(): void
    {
        $tenant = Tenant::factory()->create([
            'nama_pic' => 'Budi Santoso',
            'email' => 'budi@ujicoba.id',
        ]);

        $primary = User::factory()->tenant()->create([
            'email' => 'budi@ujicoba.id',
            'tenant_id' => $tenant->id,
        ]);

        $second = User::factory()->tenant()->create([
            'email' => 'siti@ujicoba.id',
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->put("/tenants/{$tenant->id}", $this->payload([
                'kode_tenant' => $tenant->kode_tenant,
                'nama_pic' => 'Budi Santoso Wijaya',
                'email' => 'budi.baru@ujicoba.id',
            ]))
            ->assertRedirect('/tenants');

        $this->assertSame('budi.baru@ujicoba.id', $primary->fresh()->email);
        $this->assertSame('Budi Santoso Wijaya', $primary->fresh()->name);
        $this->assertSame('siti@ujicoba.id', $second->fresh()->email);
    }

    public function test_verifying_a_tenant_activates_its_pending_contract(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'Menunggu Verifikasi']);

        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'Menunggu Verifikasi',
        ]);

        $this->actingAs(User::factory()->create())
            ->put("/tenants/{$tenant->id}", $this->payload([
                'kode_tenant' => $tenant->kode_tenant,
                'email' => $tenant->email,
                'status' => 'Aktif',
            ]))
            ->assertRedirect('/tenants');

        $this->assertSame('Aktif', $contract->fresh()->status);
    }

    public function test_index_shows_the_period_from_the_active_contract(): void
    {
        $tenant = Tenant::factory()->create();

        Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'Aktif',
            'tanggal_mulai' => '2024-01-01',
            'tanggal_berakhir' => '2024-12-31',
        ]);

        Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'Aktif',
            'tanggal_mulai' => '2025-02-01',
            'tanggal_berakhir' => '2026-01-31',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/tenants')
            ->assertOk()
            ->assertSee('01/02/2025')
            ->assertSee('31/01/2026')
            ->assertDontSee('01/01/2024');
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
            'alamat' => 'Jl. Uji No. 1, Bogor',
            'no_hp' => '081200011122',
            'email' => 'rina@ujicoba.id',
            'status' => 'Aktif',
        ], $overrides);
    }
}
