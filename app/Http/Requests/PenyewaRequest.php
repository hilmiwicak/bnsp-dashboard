<?php

namespace App\Http\Requests;

use App\Models\Penyewa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenyewaRequest extends FormRequest
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
        $ignoreId = $this->penyewaBeingUpdated()?->id;

        return [
            'kode_penyewa' => [
                'required', 'string', 'max:20',
                Rule::unique('penyewa', 'kode_penyewa')->ignore($ignoreId),
            ],
            'nama_pic' => ['required', 'string', 'max:255'],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'nib_npwp' => ['nullable', 'string', 'max:40'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', Rule::in(Penyewa::JENIS_KELAMIN)],
            'alamat' => ['required', 'string', 'max:500'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-()\s]+$/'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('penyewa', 'email')->ignore($ignoreId),
            ],
            'paket_layanan' => ['required', Rule::in(Penyewa::PAKET_LAYANAN)],
            'tanggal_mulai_sewa' => ['required', 'date'],
            'durasi_sewa' => ['required', 'integer', 'min:1', 'max:120'],
            'status' => ['required', Rule::in(Penyewa::STATUS)],
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
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'jenis_kelamin' => 'jenis kelamin',
            'no_hp' => 'nomor HP',
            'paket_layanan' => 'paket layanan',
            'tanggal_mulai_sewa' => 'tanggal mulai sewa',
            'durasi_sewa' => 'durasi sewa',
        ];
    }

    /**
     * The penyewa currently being edited, when the route model is already bound.
     */
    private function penyewaBeingUpdated(): ?Penyewa
    {
        $penyewa = $this->route('penyewa');

        return $penyewa instanceof Penyewa ? $penyewa : null;
    }
}
