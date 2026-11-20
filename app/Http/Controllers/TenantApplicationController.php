<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\TenantApplicationRequest;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantApplicationController extends Controller
{
    /**
     * Formulir pengajuan sewa untuk customer yang belum punya data tenant.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->tenant_id) {
            return redirect()->route('portal');
        }

        return view('portal.apply', [
            'tenant' => new Tenant([
                'nama_pic' => $request->user()->name,
                'email' => $request->user()->email,
                'durasi_sewa' => 12,
            ]),
        ]);
    }

    /**
     * Simpan pengajuan sebagai data tenant berstatus menunggu verifikasi.
     */
    public function store(TenantApplicationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->tenant_id) {
            return redirect()->route('portal');
        }

        $tenant = Tenant::create([
            ...$request->validated(),
            'kode_tenant' => Tenant::nextKodeTenant(),
            'status' => 'Menunggu Verifikasi',
        ]);

        // Akun pendaftar resmi menjadi tenant dan terhubung ke data sewanya.
        $user->update([
            'role' => UserRole::Tenant,
            'tenant_id' => $tenant->id,
        ]);

        return redirect()
            ->route('portal')
            ->with('status', "Pengajuan sewa {$tenant->kode_tenant} berhasil dikirim dan menunggu verifikasi admin.");
    }
}
