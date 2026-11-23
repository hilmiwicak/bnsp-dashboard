<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TagihanRuang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceSuggestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_approved_and_finished_bookings_are_billed(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 100000]);

        $this->booking($tenant, $room, '2026-03-10', '09:00', '11:00', 'Disetujui');
        $this->booking($tenant, $room, '2026-03-11', '13:00', '14:00', 'Selesai');
        $this->booking($tenant, $room, '2026-03-12', '15:00', '16:00', 'Ditolak');
        $this->booking($tenant, $room, '2026-03-13', '17:00', '18:00', 'Menunggu');

        $ringkasan = TagihanRuang::ringkasan($tenant->id, '2026-03');

        $this->assertSame(2, $ringkasan['pemesanan']);
        $this->assertSame(180, $ringkasan['menit']);
        $this->assertSame(300000, $ringkasan['total']);
    }

    public function test_partial_hours_are_prorated(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 100000]);

        // 09:00-10:30 = 90 menit = 1,5 jam.
        $this->booking($tenant, $room, '2026-03-10', '09:00', '10:30');

        $this->assertSame(150000, TagihanRuang::ringkasan($tenant->id, '2026-03')['total']);
    }

    public function test_bookings_outside_the_period_are_ignored(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 100000]);

        $this->booking($tenant, $room, '2026-03-31', '09:00', '10:00');
        $this->booking($tenant, $room, '2026-04-01', '09:00', '10:00');

        $this->assertSame(100000, TagihanRuang::ringkasan($tenant->id, '2026-03')['total']);
    }

    public function test_the_snapshotted_rate_wins_over_the_current_room_rate(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 999000]);

        $this->booking($tenant, $room, '2026-03-10', '09:00', '10:00', 'Disetujui', 50000);

        $this->assertSame(50000, TagihanRuang::ringkasan($tenant->id, '2026-03')['total']);
    }

    public function test_the_suggested_amount_is_prefilled_on_the_create_page(): void
    {
        $tenant = Tenant::factory()->create();
        $room = Room::factory()->create(['harga_per_jam' => 100000]);

        $this->booking($tenant, $room, '2026-03-10', '09:00', '11:00');

        $this->actingAs(User::factory()->create())
            ->get("/tagihan-pembayaran/create?tenant_id={$tenant->id}&periode=2026-03")
            ->assertOk()
            ->assertSee('1 pemesanan')
            ->assertSee('Rp200.000')
            ->assertSee('value="200000"', false);
    }

    public function test_a_duplicate_period_is_warned_about(): void
    {
        $tenant = Tenant::factory()->create();

        Invoice::factory()->create([
            'tenant_id' => $tenant->id,
            'periode' => '2026-03',
        ]);

        $this->actingAs(User::factory()->create())
            ->get("/tagihan-pembayaran/create?tenant_id={$tenant->id}&periode=2026-03")
            ->assertOk()
            ->assertSee('Sudah ada 1 tagihan');
    }

    public function test_an_empty_period_shows_no_suggestion(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get("/tagihan-pembayaran/create?tenant_id={$tenant->id}&periode=2026-03")
            ->assertOk()
            ->assertSee('Tidak ada pemesanan');
    }

    private function booking(
        Tenant $tenant,
        Room $room,
        string $tanggal,
        string $mulai,
        string $selesai,
        string $status = 'Disetujui',
        ?int $tarif = null,
    ): Booking {
        return Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'harga_per_jam' => $tarif ?? $room->harga_per_jam,
            'tanggal' => $tanggal,
            'jam_mulai' => $mulai,
            'jam_selesai' => $selesai,
            'jumlah_peserta' => 2,
            'status' => $status,
        ]);
    }
}
