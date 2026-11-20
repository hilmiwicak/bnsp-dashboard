<?php

namespace App\Http\Requests;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractRequest extends FormRequest
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
        $ignoreId = $this->contractBeingUpdated()?->id;

        return [
            'nomor_kontrak' => [
                'required', 'string', 'max:20',
                Rule::unique('contracts', 'nomor_kontrak')->ignore($ignoreId),
            ],
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'nilai_sewa' => ['required', 'integer', 'min:0', 'max:100000000000'],
            'status' => ['required', Rule::in(Contract::STATUS)],
            'catatan' => ['nullable', 'string', 'max:500'],
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
            'nomor_kontrak' => 'nomor kontrak',
            'tenant_id' => 'tenant',
            'tanggal_mulai' => 'tanggal mulai',
            'tanggal_berakhir' => 'tanggal berakhir',
            'nilai_sewa' => 'nilai sewa',
        ];
    }

    /**
     * The contract currently being edited, when the route model is already bound.
     */
    private function contractBeingUpdated(): ?Contract
    {
        $contract = $this->route('contract');

        return $contract instanceof Contract ? $contract : null;
    }
}
