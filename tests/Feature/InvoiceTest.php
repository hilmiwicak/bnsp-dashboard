<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_pages_are_only_reachable_by_admins(): void
    {
        $this->get('/tagihan-pembayaran')->assertRedirect('/login');

        $this->actingAs(User::factory()->customer()->create())
            ->get('/tagihan-pembayaran')
            ->assertForbidden();
    }

    public function test_index_lists_invoices(): void
    {
        $tenant = Tenant::factory()->create(['nama_perusahaan' => 'PT Nusantara Digital']);

        Invoice::factory()->create([
            'nomor_tagihan' => 'INV-900',
            'tenant_id' => $tenant->id,
            'periode' => '2026-11',
            'jumlah' => 2500000,
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/tagihan-pembayaran')
            ->assertOk()
            ->assertSee('Daftar Tagihan')
            ->assertSee('INV-900')
            ->assertSee('PT Nusantara Digital')
            ->assertSee('November 2026')
            ->assertSee('Rp2.500.000');
    }

    public function test_index_can_be_searched_and_filtered(): void
    {
        $tenant = Tenant::factory()->create();

        Invoice::factory()->create([
            'nomor_tagihan' => 'INV-901',
            'tenant_id' => $tenant->id,
            'status' => 'Lunas',
            'tanggal_bayar' => now()->toDateString(),
        ]);

        Invoice::factory()->create([
            'nomor_tagihan' => 'INV-902',
            'status' => 'Belum Dibayar',
        ]);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/tagihan-pembayaran?q=INV-901')
            ->assertOk()
            ->assertSee('INV-901')
            ->assertDontSee('INV-902');

        $this->actingAs($admin)
            ->get('/tagihan-pembayaran?status=Lunas')
            ->assertOk()
            ->assertSee('INV-901')
            ->assertDontSee('INV-902');

        $this->actingAs($admin)
            ->get("/tagihan-pembayaran?tenant={$tenant->id}")
            ->assertOk()
            ->assertSee('INV-901')
            ->assertDontSee('INV-902');
    }

    public function test_create_and_edit_screens_are_reachable(): void
    {
        $invoice = Invoice::factory()->create(['nomor_tagihan' => 'INV-903']);

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/tagihan-pembayaran/create')
            ->assertOk()
            ->assertSee('Informasi Tagihan')
            ->assertSee('Jatuh Tempo')
            ->assertSee('Tanggal Bayar');

        $this->actingAs($admin)
            ->get("/tagihan-pembayaran/{$invoice->id}/edit")
            ->assertOk()
            ->assertSee('Edit Tagihan')
            ->assertSee('INV-903');
    }

    public function test_an_invoice_can_be_stored(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran', $this->payload(['tenant_id' => $tenant->id]))
            ->assertRedirect('/tagihan-pembayaran')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('invoices', [
            'nomor_tagihan' => 'INV-100',
            'tenant_id' => $tenant->id,
            'periode' => '2026-11',
            'status' => 'Belum Dibayar',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran', [])
            ->assertSessionHasErrors([
                'nomor_tagihan',
                'tenant_id',
                'periode',
                'jumlah',
                'jatuh_tempo',
                'status',
            ]);

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_the_period_must_follow_the_year_month_format(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran', $this->payload([
                'tenant_id' => $tenant->id,
                'periode' => '11/2026',
            ]))
            ->assertSessionHasErrors(['periode']);
    }

    public function test_the_invoice_number_must_be_unique(): void
    {
        Invoice::factory()->create(['nomor_tagihan' => 'INV-101']);

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran', $this->payload(['nomor_tagihan' => 'INV-101']))
            ->assertSessionHasErrors(['nomor_tagihan']);
    }

    public function test_a_paid_invoice_gets_a_payment_date_automatically(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran', $this->payload([
                'tenant_id' => $tenant->id,
                'status' => 'Lunas',
                'tanggal_bayar' => '',
            ]))
            ->assertRedirect('/tagihan-pembayaran');

        $invoice = Invoice::firstWhere('nomor_tagihan', 'INV-100');

        $this->assertSame('Lunas', $invoice->status);
        $this->assertNotNull($invoice->tanggal_bayar);
    }

    public function test_an_invoice_can_be_updated(): void
    {
        $invoice = Invoice::factory()->create([
            'nomor_tagihan' => 'INV-104',
            'status' => 'Belum Dibayar',
        ]);

        $this->actingAs(User::factory()->create())
            ->put("/tagihan-pembayaran/{$invoice->id}", $this->payload([
                'nomor_tagihan' => 'INV-104',
                'tenant_id' => $invoice->tenant_id,
                'jumlah' => 3000000,
            ]))
            ->assertRedirect('/tagihan-pembayaran')
            ->assertSessionHas('status');

        $this->assertSame(3000000, $invoice->fresh()->jumlah);
    }

    public function test_an_invoice_can_be_marked_paid_with_one_click(): void
    {
        $invoice = Invoice::factory()->create([
            'status' => 'Belum Dibayar',
            'tanggal_bayar' => null,
        ]);

        $this->actingAs(User::factory()->create())
            ->patch("/tagihan-pembayaran/{$invoice->id}/lunas")
            ->assertRedirect('/tagihan-pembayaran')
            ->assertSessionHas('status');

        $invoice->refresh();

        $this->assertSame('Lunas', $invoice->status);
        $this->assertSame(now()->toDateString(), $invoice->tanggal_bayar->toDateString());
    }

    public function test_an_invoice_can_be_deleted(): void
    {
        $invoice = Invoice::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete("/tagihan-pembayaran/{$invoice->id}")
            ->assertRedirect('/tagihan-pembayaran')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_the_unpaid_total_only_counts_open_invoices(): void
    {
        Invoice::factory()->create(['jumlah' => 2000000, 'status' => 'Belum Dibayar']);
        Invoice::factory()->create(['jumlah' => 1500000, 'status' => 'Terlambat']);
        Invoice::factory()->lunas()->create(['jumlah' => 9000000]);

        $this->actingAs(User::factory()->create())
            ->get('/tagihan-pembayaran')
            ->assertOk()
            ->assertSee('Rp3.500.000 belum lunas');
    }

    public function test_the_next_invoice_number_follows_the_sequence(): void
    {
        $this->assertSame('INV-001', Invoice::nextKodeTagihan());

        Invoice::factory()->create(['nomor_tagihan' => 'INV-042']);

        $this->assertSame('INV-043', Invoice::nextKodeTagihan());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nomor_tagihan' => 'INV-100',
            'tenant_id' => Tenant::factory()->create()->id,
            'periode' => '2026-11',
            'jumlah' => 2500000,
            'jatuh_tempo' => '2026-11-30',
            'tanggal_bayar' => null,
            'status' => 'Belum Dibayar',
        ], $overrides);
    }
}
