<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_lists_every_standard_kantor_virtual_menu(): void
    {
        $labels = [
            'Dashboard',
            'Data Tenant',
            'Data Ruang & Fasilitas',
            'Pemesanan',
            'Kontrak Sewa',
            'Jadwal Penggunaan Ruang',
            'Tagihan & Pembayaran',
            'Laporan',
        ];

        $response = $this->actingAs(User::factory()->create())->get('/dashboard');

        $response->assertOk();

        foreach ($labels as $label) {
            $response->assertSee($label);
        }
    }

    public function test_every_module_page_is_reachable(): void
    {
        $slugs = [
            'ruang-fasilitas',
            'pemesanan',
            'kontrak-sewa',
            'jadwal-ruang',
            'tagihan-pembayaran',
            'laporan',
        ];

        $user = User::factory()->create();

        foreach ($slugs as $slug) {
            $this->actingAs($user)
                ->get("/{$slug}")
                ->assertOk()
                ->assertSee('Tahap berikutnya');
        }
    }

    public function test_menu_pages_require_authentication(): void
    {
        $this->get('/ruang-fasilitas')->assertRedirect('/login');
        $this->get('/laporan')->assertRedirect('/login');
    }

    public function test_root_url_leads_to_the_dashboard(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
    }

    public function test_active_menu_item_is_marked_with_aria_current(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/tenants');

        $response->assertOk();
        $response->assertSee('aria-current="page"', false);
    }
}
