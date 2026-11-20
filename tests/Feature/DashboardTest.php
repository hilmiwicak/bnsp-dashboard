<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_dashboard_is_only_reachable_by_admins(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');

        $this->actingAs(User::factory()->customer()->create())
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_the_dashboard_renders_every_summary_section(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Total Tenant')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Pemesanan 7 Hari')
            ->assertSee('Pendapatan Lunas')
            ->assertSee('Aktivitas Pemesanan')
            ->assertSee('Status Tenant')
            ->assertSee('Riwayat Pendapatan')
            ->assertSee('Tenant Terbaru');
    }

    public function test_tenant_statistics_come_from_the_database(): void
    {
        Tenant::factory()->count(2)->create(['status' => 'Aktif']);
        Tenant::factory()->create(['status' => 'Menunggu Verifikasi']);
        Tenant::factory()->create(['status' => 'Nonaktif']);

        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('2 tenant aktif')
            ->assertSee('Perlu ditinjau admin');
    }

    public function test_the_outstanding_bill_total_is_shown(): void
    {
        Invoice::factory()->create(['jumlah' => 1000000, 'status' => 'Belum Dibayar']);
        Invoice::factory()->create(['jumlah' => 500000, 'status' => 'Terlambat']);
        Invoice::factory()->lunas()->create(['jumlah' => 2500000]);

        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Rp1.500.000 belum lunas')
            ->assertSee('Rp2.500.000');
    }

    public function test_only_the_five_most_recent_tenants_are_listed(): void
    {
        Tenant::factory()->create([
            'nama_perusahaan' => 'PT Tenant Lama',
            'created_at' => now()->subMonths(6),
        ]);

        Tenant::factory()->count(5)->create(['created_at' => now()]);

        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('PT Tenant Lama');
    }

    public function test_the_activity_chart_counts_recent_bookings(): void
    {
        Booking::factory()->disetujui()->create(['tanggal' => now()->toDateString()]);
        Booking::factory()->create([
            'tanggal' => now()->toDateString(),
            'status' => 'Menunggu',
        ]);
        Booking::factory()->disetujui()->create(['tanggal' => now()->subMonth()->toDateString()]);

        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tujuh hari terakhir')
            ->assertSee('Disetujui');
    }

    public function test_empty_states_are_shown_when_there_is_no_data(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Belum ada tenant.')
            ->assertSee('Belum ada tenant terdaftar.');
    }
}
