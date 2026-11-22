<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TenantApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_open_the_application_form(): void
    {
        $this->actingAs(User::factory()->tenant()->create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
        ]))
            ->get('/portal/pengajuan')
            ->assertOk()
            ->assertSee('Informasi PIC')
            ->assertSee('Kirim Pengajuan')
            ->assertSee('Dewi Lestari')
            ->assertSee('dewi@example.com')
            ->assertDontSee('Kode Tenant')
            ->assertDontSee('Status Tenant');
    }

    public function test_submitting_the_form_creates_a_pending_tenant_and_links_the_account(): void
    {
        $user = User::factory()->tenant()->create(['name' => 'Dewi Lestari']);
        $payload = $this->payload();

        $this->actingAs($user)
            ->post('/portal/pengajuan', $payload)
            ->assertRedirect('/portal')
            ->assertSessionHas('status');

        $tenant = Tenant::where('email', 'dewi@example.com')->firstOrFail();

        $this->assertSame('Menunggu Verifikasi', $tenant->status);
        $this->assertSame('TNT-001', $tenant->kode_tenant);
        $this->assertSame($tenant->id, $user->fresh()->tenant_id);
        $this->assertSame(UserRole::Tenant, $user->fresh()->role);

        $contract = $tenant->contracts()->firstOrFail();

        $this->assertSame('Menunggu Verifikasi', $contract->status);
        $this->assertSame($payload['tanggal_mulai_sewa'], $contract->tanggal_mulai->toDateString());
        $this->assertSame(
            Carbon::parse($payload['tanggal_mulai_sewa'])->addMonths(12)->toDateString(),
            $contract->tanggal_berakhir->toDateString(),
        );
    }

    public function test_the_portal_then_shows_the_submitted_tenancy(): void
    {
        $user = User::factory()->tenant()->create();

        $this->actingAs($user)->post('/portal/pengajuan', $this->payload());

        $this->actingAs($user)
            ->get('/portal')
            ->assertOk()
            ->assertSee('Data Sewa')
            ->assertSee('Menunggu verifikasi admin')
            ->assertDontSee('Langkah Selanjutnya');
    }

    public function test_application_is_validated(): void
    {
        $this->actingAs(User::factory()->tenant()->create())
            ->post('/portal/pengajuan', [])
            ->assertSessionHasErrors([
                'nama_pic',
                'nama_perusahaan',
                'alamat',
                'no_hp',
                'email',
                'tanggal_mulai_sewa',
                'durasi_sewa',
            ]);

        $this->assertDatabaseCount('tenants', 0);
    }

    public function test_validation_errors_are_shown_on_the_application_form(): void
    {
        $this->actingAs(User::factory()->tenant()->create())
            ->post('/portal/pengajuan', ['durasi_sewa' => 0]);

        $this->get('/portal/pengajuan')
            ->assertOk()
            ->assertSee('The durasi sewa field must be at least 1.')
            ->assertSee('The nama lengkap PIC field is required.');
    }

    public function test_the_start_date_cannot_be_in_the_past(): void
    {
        $this->actingAs(User::factory()->tenant()->create())
            ->post('/portal/pengajuan', $this->payload([
                'tanggal_mulai_sewa' => now()->subMonth()->toDateString(),
            ]))
            ->assertSessionHasErrors(['tanggal_mulai_sewa']);

        $this->assertDatabaseCount('tenants', 0);
    }

    public function test_the_email_must_not_belong_to_another_tenant(): void
    {
        Tenant::factory()->create(['email' => 'dewi@example.com']);

        $this->actingAs(User::factory()->tenant()->create())
            ->post('/portal/pengajuan', $this->payload())
            ->assertSessionHasErrors(['email']);
    }

    public function test_an_account_with_an_existing_tenancy_cannot_apply_again(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->tenant()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user)->get('/portal/pengajuan')->assertRedirect('/portal');

        $this->actingAs($user)
            ->post('/portal/pengajuan', $this->payload())
            ->assertRedirect('/portal');

        $this->assertSame(1, Tenant::count());
        $this->assertSame($tenant->id, $user->fresh()->tenant_id);
    }

    public function test_admins_and_guests_cannot_open_the_application_form(): void
    {
        $this->get('/portal/pengajuan')->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->get('/portal/pengajuan')
            ->assertForbidden();
    }

    public function test_the_portal_links_to_the_application_form(): void
    {
        $this->actingAs(User::factory()->tenant()->create())
            ->get('/portal')
            ->assertOk()
            ->assertSee('Ajukan Sewa');
    }

    public function test_the_next_tenant_code_follows_the_existing_sequence(): void
    {
        $this->assertSame('TNT-001', Tenant::nextKodeTenant());

        Tenant::factory()->create(['kode_tenant' => 'TNT-007']);

        $this->assertSame('TNT-008', Tenant::nextKodeTenant());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nama_pic' => 'Dewi Lestari',
            'nama_perusahaan' => 'PT Karya Mandiri',
            'nib_npwp' => '1234567890123',
            'alamat' => 'Jl. Merdeka No. 10, Bandung',
            'no_hp' => '081200011133',
            'email' => 'dewi@example.com',
            'tanggal_mulai_sewa' => now()->addWeek()->toDateString(),
            'durasi_sewa' => 12,
        ], $overrides);
    }
}
