<?php

namespace App\Http\Requests;

use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $ignoreId = $this->tenantBeingUpdated()?->id;

        // Periode sewa hanya diminta saat menambah tenant baru; kontrak
        // berikutnya dikelola lewat modul Kontrak Sewa.
        $creating = $this->tenantBeingUpdated() === null;

        return [
            'kode_tenant' => [
                'required', 'string', 'max:20',
                Rule::unique('tenants', 'kode_tenant')->ignore($ignoreId),
            ],
            'nama_pic' => ['required', 'string', 'max:255'],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'nib_npwp' => ['nullable', 'string', 'max:40'],
            'alamat' => ['required', 'string', 'max:500'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-()\s]+$/'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('tenants', 'email')->ignore($ignoreId),
                $this->emailMustNotBelongToAnotherAccount(...),
            ],
            'status' => ['required', Rule::in(Tenant::STATUS)],
            'tanggal_mulai_sewa' => $creating ? ['required', 'date'] : ['nullable'],
            'durasi_sewa' => $creating ? ['required', 'integer', 'min:1', 'max:120'] : ['nullable'],
        ];
    }

    /**
     * Human readable attribute names used in validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_pic' => 'nama lengkap PIC',
            'nama_perusahaan' => 'nama perusahaan',
            'nib_npwp' => 'NIB/NPWP',
            'no_hp' => 'nomor HP',
            'tanggal_mulai_sewa' => 'tanggal mulai sewa',
            'durasi_sewa' => 'durasi sewa',
        ];
    }

    /**
     * Tolak email milik akun yang sudah terhubung ke tenant lain.
     */
    private function emailMustNotBelongToAnotherAccount(string $attribute, mixed $value, Closure $fail): void
    {
        $account = User::query()->where('email', $value)->first();

        if (! $account) {
            return;
        }

        $current = $this->tenantBeingUpdated();

        // Akun milik tenant ini sendiri — tidak masalah.
        if ($current && $account->tenant_id === $current->id) {
            return;
        }

        // Saat menambah tenant baru, akun yang belum terhubung boleh dipakai
        // dan akan dihubungkan otomatis ke data tenant baru.
        if (! $current && $account->tenant_id === null) {
            return;
        }

        $fail('This email is already linked to another account.');
    }

    /**
     * The tenant currently being edited, when the route model is already bound.
     */
    private function tenantBeingUpdated(): ?Tenant
    {
        $tenant = $this->route('tenant');

        return $tenant instanceof Tenant ? $tenant : null;
    }
}
