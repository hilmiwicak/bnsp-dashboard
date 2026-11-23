<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceBatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_range_creates_one_invoice_per_month(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 100000]);

        // November: 1 jam, Desember: 2 jam.
        $this->booking($tenant, $room, '2026-11-10', '09:00', '10:00');
        $this->booking($tenant, $room, '2026-12-10', '09:00', '11:00');

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran/generate', [
                'tenant_id' => $tenant->id,
                'periode_awal' => '2026-11',
                'periode_akhir' => '2026-12',
                'catatan' => 'Pemakaian ruang.',
            ])
            ->assertRedirect('/tagihan-pembayaran')
            ->assertSessionHas('status');

        $this->assertDatabaseCount('invoices', 2);

        $november = Invoice::where('periode', '2026-11')->firstOrFail();
        $desember = Invoice::where('periode', '2026-12')->firstOrFail();

        $this->assertSame(100000, $november->jumlah);
        $this->assertSame(200000, $desember->jumlah);
        $this->assertSame('2026-11-30', $november->jatuh_tempo->toDateString());
        $this->assertSame('2026-12-31', $desember->jatuh_tempo->toDateString());
        $this->assertSame('Belum Dibayar', $november->status);
        $this->assertSame('Pemakaian ruang.', $november->catatan);
        $this->assertSame($tenant->id, $november->tenant_id);
        $this->assertNotSame($november->nomor_tagihan, $desember->nomor_tagihan);
    }

    public function test_months_without_bookings_are_skipped(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 100000]);

        $this->booking($tenant, $room, '2026-11-10', '09:00', '10:00');

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran/generate', [
                'tenant_id' => $tenant->id,
                'periode_awal' => '2026-11',
                'periode_akhir' => '2026-12',
            ])
            ->assertRedirect('/tagihan-pembayaran');

        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseHas('invoices', ['periode' => '2026-11']);
        $this->assertDatabaseMissing('invoices', ['periode' => '2026-12']);
    }

    public function test_months_that_already_have_an_invoice_are_skipped(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 100000]);

        $this->booking($tenant, $room, '2026-11-10', '09:00', '10:00');
        $this->booking($tenant, $room, '2026-12-10', '09:00', '10:00');

        Invoice::factory()->create([
            'tenant_id' => $tenant->id,
            'periode' => '2026-12',
            'jumlah' => 111111,
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran/generate', [
                'tenant_id' => $tenant->id,
                'periode_awal' => '2026-11',
                'periode_akhir' => '2026-12',
            ])
            ->assertRedirect('/tagihan-pembayaran');

        // Hanya November yang baru; Desember dibiarkan apa adanya.
        $this->assertDatabaseCount('invoices', 2);
        $this->assertSame(111111, Invoice::where('periode', '2026-12')->firstOrFail()->jumlah);
    }

    public function test_the_range_preview_is_shown_on_the_create_page(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 100000]);

        $this->booking($tenant, $room, '2026-11-10', '09:00', '10:00');
        $this->booking($tenant, $room, '2026-12-10', '09:00', '11:00');

        $this->actingAs(User::factory()->create())
            ->get("/tagihan-pembayaran/create?tenant_id={$tenant->id}&periode=2026-11&periode_akhir=2026-12")
            ->assertOk()
            ->assertSee('2 bulan dipilih')
            ->assertSee('Terbitkan 2 Tagihan')
            ->assertSee('Rp300.000');
    }

    public function test_a_reversed_range_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran/generate', [
                'tenant_id' => $tenant->id,
                'periode_awal' => '2026-12',
                'periode_akhir' => '2026-11',
            ])
            ->assertSessionHasErrors(['periode_akhir']);

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_a_malformed_period_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran/generate', [
                'tenant_id' => $tenant->id,
                'periode_awal' => '2026-1',
                'periode_akhir' => '2026-12',
            ])
            ->assertSessionHasErrors(['periode_awal']);

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_a_range_longer_than_the_limit_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post('/tagihan-pembayaran/generate', [
                'tenant_id' => $tenant->id,
                'periode_awal' => '2024-01',
                'periode_akhir' => '2026-12',
            ])
            ->assertSessionHasErrors(['periode_akhir']);

        $this->assertDatabaseCount('invoices', 0);
    }

    private function booking(
        Tenant $tenant,
        Room $room,
        string $tanggal,
        string $mulai,
        string $selesai,
    ): Booking {
        return Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'harga_per_jam' => $room->harga_per_jam,
            'tanggal' => $tanggal,
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'jumlah_peserta' => 2,
            'status' => 'Disetujui',
        ]);
    }
}
