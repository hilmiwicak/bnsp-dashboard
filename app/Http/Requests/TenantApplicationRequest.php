<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantApplicationRequest extends FormRequest
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
        return [
            'nama_pic' => ['required', 'string', 'max:255'],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'nib_npwp' => ['nullable', 'string', 'max:40'],
            'alamat' => ['required', 'string', 'max:500'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-()\s]+$/'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('tenants', 'email'),
            ],
            'tanggal_mulai_sewa' => ['required', 'date', 'after_or_equal:today'],
            'durasi_sewa' => ['required', 'integer', 'min:1', 'max:120'],
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
}
