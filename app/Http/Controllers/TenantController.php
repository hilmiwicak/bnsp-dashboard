<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\TenantRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Tampilkan daftar tenant beserta pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'status' => $this->allowedFilter($request->query('status'), Tenant::STATUS),
        ];

        $tenant = Tenant::query()
            ->with('activeContract')
            ->search($filters['q'])
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        return view('tenants.index', [
            'tenant' => $tenant,
            'filters' => $filters,
            'totalSemua' => Tenant::count(),
        ]);
    }

    /**
     * Tampilkan formulir tambah tenant.
     */
    public function create(): View
    {
        return view('tenants.create', [
            'tenant' => new Tenant([
                'kode_tenant' => Tenant::nextKodeTenant(),
                'status' => 'Menunggu Verifikasi',
            ]),
        ]);
    }

    /**
     * Simpan data tenant baru beserta akun login PIC-nya.
     */
    public function store(TenantRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $password = null;

        $tenant = DB::transaction(function () use ($validated, &$password) {
            $tenant = Tenant::create($validated);

            $account = User::query()->where('email', $validated['email'])->first();

            if ($account) {
                // PIC sudah punya akun (mendaftar sendiri lebih dulu): hubungkan
                // ke data tenant ini tanpa mengganti password lamanya.
                $account->update([
                    'name' => $validated['nama_pic'],
                    'role' => UserRole::Tenant,
                    'tenant_id' => $tenant->id,
                ]);
            } else {
                $password = Str::password(12, symbols: false);

                User::create([
                    'name' => $validated['nama_pic'],
                    'email' => $validated['email'],
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => UserRole::Tenant,
                    'tenant_id' => $tenant->id,
                ]);
            }

            return $tenant;
        });

        $redirect = redirect()
            ->route('tenants.index')
            ->with('status', "Data tenant {$tenant->nama_pic} berhasil ditambahkan.");

        // Password sementara hanya dikirim sekali lewat session, tidak disimpan
        // dalam bentuk terbaca di mana pun.
        if ($password === null) {
            return $redirect;
        }

        return $redirect->with('kredensial', [
            'kode_tenant' => $tenant->kode_tenant,
            'email' => $validated['email'],
            'password' => $password,
        ]);
    }

    /**
     * Tampilkan formulir ubah data tenant.
     */
    public function edit(Tenant $tenant): View
    {
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Perbarui data tenant.
     */
    public function update(TenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $tenant->update($request->validated());

        // Jaga agar akun login PIC tetap sinkron dengan data tenant.
        User::query()
            ->where('tenant_id', $tenant->id)
            ->update([
                'name' => $tenant->nama_pic,
                'email' => $tenant->email,
            ]);

        // Verifikasi tenant sekaligus mengaktifkan kontrak pengajuannya.
        if ($tenant->status === 'Aktif') {
            $tenant->contracts()
                ->where('status', 'Menunggu Verifikasi')
                ->update(['status' => 'Aktif']);
        }

        return redirect()
            ->route('tenants.index')
            ->with('status', "Data tenant {$tenant->nama_pic} berhasil diperbarui.");
    }

    /**
     * Hapus data tenant.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $nama = $tenant->nama_pic;

        $tenant->delete();

        return redirect()
            ->route('tenants.index')
            ->with('status', "Data tenant {$nama} berhasil dihapus.");
    }
}
