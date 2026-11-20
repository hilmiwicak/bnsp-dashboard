<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_report_is_only_reachable_by_admins(): void
    {
        $this->get('/laporan')->assertRedirect('/login');

        $this->actingAs(User::factory()->tenant()->create())
            ->get('/laporan')
            ->assertForbidden();
    }

    public function test_the_report_renders_the_yearly_summary(): void
    {
        $tahun = now()->year;

        $this->actingAs(User::factory()->create())
            ->get('/laporan')
            ->assertOk()
            ->assertSee('Laporan Tahunan')
            ->assertSee('Rekap Bulanan '.$tahun)
            ->assertSee('Status Pemesanan '.$tahun)
            ->assertSee('Total Pemesanan')
            ->assertSee('Pendapatan Lunas')
            ->assertSee('Tagihan Belum Lunas');
    }

    public function test_monthly_figures_are_aggregated_from_invoices_and_bookings(): void
    {
        $tahun = now()->year;

        Invoice::factory()->create([
            'periode' => "{$tahun}-05",
            'jumlah' => 2000000,
            'status' => 'Lunas',
            'tanggal_bayar' => "{$tahun}-05-12",
            'jatuh_tempo' => "{$tahun}-05-31",
        ]);

        Invoice::factory()->create([
            'periode' => "{$tahun}-05",
            'jumlah' => 500000,
            'status' => 'Belum Dibayar',
            'jatuh_tempo' => "{$tahun}-05-31",
        ]);

        Booking::factory()->create(['tanggal' => "{$tahun}-05-04"]);
        Booking::factory()->create(['tanggal' => "{$tahun}-05-18"]);

        $this->actingAs(User::factory()->create())
            ->get('/laporan')
            ->assertOk()
            ->assertSee('Mei')
            ->assertSee('Rp2.000.000')
            ->assertSee('Rp500.000');
    }

    public function test_the_report_can_be_filtered_by_year(): void
    {
        $tahunLalu = now()->year - 1;
        $tahunIni = now()->year;

        Invoice::factory()->create([
            'periode' => "{$tahunLalu}-03",
            'jumlah' => 1000000,
            'status' => 'Lunas',
            'tanggal_bayar' => "{$tahunLalu}-03-10",
            'jatuh_tempo' => "{$tahunLalu}-03-31",
        ]);

        Invoice::factory()->create([
            'periode' => "{$tahunIni}-03",
            'jumlah' => 9000000,
            'status' => 'Lunas',
            'tanggal_bayar' => "{$tahunIni}-03-10",
            'jatuh_tempo' => "{$tahunIni}-03-31",
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/laporan?tahun='.$tahunLalu)
            ->assertOk()
            ->assertSee('Rp1.000.000')
            ->assertDontSee('Rp9.000.000');
    }

    public function test_an_unparsable_year_falls_back_to_the_current_year(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/laporan?tahun=entah')
            ->assertOk()
            ->assertSee('Rekap Bulanan '.now()->year);
    }

    public function test_the_booking_status_breakdown_counts_the_selected_year(): void
    {
        $tahun = now()->year;

        Booking::factory()->disetujui()->create(['tanggal' => "{$tahun}-02-05"]);
        Booking::factory()->create(['tanggal' => "{$tahun}-02-06", 'status' => 'Menunggu']);
        Booking::factory()->disetujui()->create(['tanggal' => ($tahun - 1).'-02-05']);

        $this->actingAs(User::factory()->create())
            ->get('/laporan')
            ->assertOk()
            ->assertSee('Disetujui')
            ->assertSee('Menunggu');
    }
}
