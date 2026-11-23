<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_pages_are_only_reachable_by_admins(): void
    {
        $this->get('/kontrak-sewa')->assertRedirect('/login');

        $this->actingAs(User::factory()->tenant()->create())
            ->get('/kontrak-sewa')
            ->assertForbidden();
    }

    public function test_index_lists_contracts(): void
    {
        $tenant = Tenant::factory()->create(['nama_perusahaan' => 'PT Nusantara Digital']);

        Contract::factory()->create([
            'nomor_kontrak' => 'KTR-900',
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/kontrak-sewa')
            ->assertOk()
            ->assertSee('Daftar Kontrak Sewa')
            ->assertSee('KTR-900')
            ->assertSee('PT Nusantara Digital');
    }

    public function test_index_can_be_searched_and_filtered(): void
    {
        $tenant = Tenant::factory()->create(['nama_perusahaan' => 'PT Nusantara Digital']);

        Contract::factory()->create([
            'nomor_kontrak' => 'KTR-901',
            'tenant_id' => $tenant->id,
            'status' => 'Aktif',
        ]);

        Contract::factory()->create([
            'nomor_kontrak' => 'KTR-902',
            'status' => 'Berakhir',
        ]);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/kontrak-sewa?q=KTR-901')
            ->assertOk()
            ->assertSee('KTR-901')
            ->assertDontSee('KTR-902');

        $this->actingAs($admin)
            ->get('/kontrak-sewa?q=PT Nusantara Digital')
            ->assertOk()
            ->assertSee('KTR-901')
            ->assertDontSee('KTR-902');

        $this->actingAs($admin)
            ->get('/kontrak-sewa?status=Berakhir')
            ->assertOk()
            ->assertSee('KTR-902')
            ->assertDontSee('KTR-901');
    }

    public function test_create_and_edit_screens_are_reachable(): void
    {
        $contract = Contract::factory()->create(['nomor_kontrak' => 'KTR-903']);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/kontrak-sewa/create')
            ->assertOk()
            ->assertSee('Informasi Kontrak')
            ->assertSee('Periode')
            ->assertSee('Nilai Sewa');

        $this->actingAs($admin)
            ->get("/kontrak-sewa/{$contract->id}/edit")
            ->assertOk()
            ->assertSee('Edit Kontrak Sewa')
            ->assertSee('KTR-903');
    }

    public function test_a_contract_can_be_stored(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/kontrak-sewa', $this->payload(['tenant_id' => $tenant->id]))
            ->assertRedirect('/kontrak-sewa')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('contracts', [
            'nomor_kontrak' => 'KTR-100',
            'tenant_id' => $tenant->id,
            'status' => 'Aktif',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/kontrak-sewa', [])
            ->assertSessionHasErrors([
                'nomor_kontrak',
                'tenant_id',
                'tanggal_mulai',
                'tanggal_berakhir',
                'status',
            ]);

        $this->assertDatabaseCount('contracts', 0);
    }

    public function test_the_end_date_must_not_be_before_the_start_date(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/kontrak-sewa', $this->payload([
                'tenant_id' => $tenant->id,
                'tanggal_mulai' => '2026-06-01',
                'tanggal_berakhir' => '2026-05-01',
            ]))
            ->assertSessionHasErrors(['tanggal_berakhir']);
    }

    public function test_the_contract_number_must_be_unique(): void
    {
        Contract::factory()->create(['nomor_kontrak' => 'KTR-101']);

        $this->actingAs(User::factory()->create())
            ->post('/kontrak-sewa', $this->payload(['nomor_kontrak' => 'KTR-101']))
            ->assertSessionHasErrors(['nomor_kontrak']);
    }

    public function test_a_contract_can_be_updated(): void
    {
        $contract = Contract::factory()->create([
            'nomor_kontrak' => 'KTR-104',
            'status' => 'Aktif',
        ]);

        $this->actingAs(User::factory()->create())
            ->put("/kontrak-sewa/{$contract->id}", $this->payload([
                'nomor_kontrak' => 'KTR-104',
                'tenant_id' => $contract->tenant_id,
                'status' => 'Berakhir',
            ]))
            ->assertRedirect('/kontrak-sewa')
            ->assertSessionHas('status');

        $this->assertSame('Berakhir', $contract->fresh()->status);
    }

    public function test_a_contract_can_be_deleted(): void
    {
        $contract = Contract::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete("/kontrak-sewa/{$contract->id}")
            ->assertRedirect('/kontrak-sewa')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('contracts', ['id' => $contract->id]);
    }

    public function test_create_page_exposes_tenant_start_dates_for_prefill(): void
    {
        $tenant = Tenant::factory()->create();

        Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'Aktif',
            'tanggal_berakhir' => '2025-12-31',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/kontrak-sewa/create')
            ->assertOk()
            ->assertSee('2026-01-01');
    }

    public function test_the_contract_duration_is_derived_from_its_dates(): void
    {
        $contract = Contract::factory()->create([
            'tanggal_mulai' => '2026-01-15',
            'tanggal_berakhir' => '2026-07-15',
        ]);

        $this->assertSame(6, $contract->durasiBulan());
    }

    public function test_the_next_contract_number_follows_the_sequence(): void
    {
        $this->assertSame('KTR-001', Contract::nextKodeKontrak());

        Contract::factory()->create(['nomor_kontrak' => 'KTR-042']);

        $this->assertSame('KTR-043', Contract::nextKodeKontrak());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nomor_kontrak' => 'KTR-100',
            'tenant_id' => Tenant::factory()->create()->id,
            'tanggal_mulai' => '2026-01-01',
            'tanggal_berakhir' => '2026-12-31',
            'status' => 'Aktif',
            'catatan' => 'Kontrak tahunan.',
        ], $overrides);
    }
}
