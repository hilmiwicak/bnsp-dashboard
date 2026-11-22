<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\TenantApplicationRequest;
use App\Models\Contract;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        $validated = $request->validated();
        $mulai = Carbon::parse($validated['tanggal_mulai_sewa']);

        $tenant = DB::transaction(function () use ($validated, $mulai) {
            $tenant = Tenant::create([
                ...Arr::except($validated, ['tanggal_mulai_sewa', 'durasi_sewa']),
                'kode_tenant' => Tenant::nextKodeTenant(),
                'status' => 'Menunggu Verifikasi',
            ]);

            // Periode sewa selalu berasal dari tabel kontrak, termasuk
            // pengajuan yang belum diverifikasi admin.
            $tenant->contracts()->create([
                'nomor_kontrak' => Contract::nextKodeKontrak(),
                'tanggal_mulai' => $mulai,
                'tanggal_berakhir' => $mulai->copy()->addMonths((int) $validated['durasi_sewa']),
                'nilai_sewa' => 0,
                'status' => 'Menunggu Verifikasi',
            ]);

            return $tenant;
        });

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
