<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reach_the_management_area(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/tenants')->assertOk();
        $this->actingAs($admin)->get('/pemesanan')->assertOk();
        $this->actingAs($admin)->get('/laporan')->assertOk();
    }

    public function test_non_admin_accounts_are_forbidden_from_management_pages(): void
    {
        $accounts = [
            User::factory()->tenant()->create(),
            User::factory()->customer()->create(),
        ];

        foreach ($accounts as $user) {
            $this->actingAs($user)->get('/dashboard')->assertForbidden();
            $this->actingAs($user)->get('/tenants')->assertForbidden();
            $this->actingAs($user)->get('/laporan')->assertForbidden();
        }
    }

    public function test_admin_is_forbidden_from_the_portal(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/portal')
            ->assertForbidden();
    }

    public function test_customer_sees_the_pending_state_on_the_portal(): void
    {
        $this->actingAs(User::factory()->customer()->create())
            ->get('/portal')
            ->assertOk()
            ->assertSee('Pengajuan sewa belum dibuat')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Langkah Selanjutnya');
    }

    public function test_tenant_sees_their_tenancy_summary_on_the_portal(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'Aktif']);

        $this->actingAs(User::factory()->tenant()->create(['tenant_id' => $tenant->id]))
            ->get('/portal')
            ->assertOk()
            ->assertSee('Keanggotaan aktif')
            ->assertSee('Data Sewa')
            ->assertSee($tenant->kode_tenant)
            ->assertSee($tenant->nama_perusahaan);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/portal')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/tenants')->assertRedirect('/login');
    }

    public function test_portal_sidebar_only_lists_the_portal_menu(): void
    {
        $response = $this->actingAs(User::factory()->customer()->create())->get('/portal');

        $response->assertOk();
        $response->assertSee('Portal');
        $response->assertDontSee('Data Tenant');
        $response->assertDontSee('Kontrak Sewa');
        $response->assertSee('Logout');
    }

    public function test_every_role_sees_a_logout_control(): void
    {
        $logoutUrl = 'action="'.route('logout').'"';

        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee($logoutUrl, false);

        $this->actingAs(User::factory()->tenant()->create())
            ->get('/portal')
            ->assertOk()
            ->assertSee($logoutUrl, false);

        $this->actingAs(User::factory()->customer()->create())
            ->get('/portal')
            ->assertOk()
            ->assertSee($logoutUrl, false);
    }

    public function test_login_sends_a_customer_to_the_portal(): void
    {
        $customer = User::factory()->customer()->create([
            'password' => password_hash('secret123', PASSWORD_DEFAULT),
        ]);

        $this->post('/login', [
            'email' => $customer->email,
            'password' => 'secret123',
        ])->assertRedirect('/portal');

        $this->assertAuthenticatedAs($customer);
    }

    public function test_self_registration_creates_a_customer_account(): void
    {
        $this->post('/register', [
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect('/portal');

        $this->assertTrue(User::where('email', 'dewi@example.com')->firstOrFail()->isCustomer());
    }
}
