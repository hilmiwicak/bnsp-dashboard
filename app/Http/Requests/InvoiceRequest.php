<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
        $ignoreId = $this->invoiceBeingUpdated()?->id;

        return [
            'nomor_tagihan' => [
                'required', 'string', 'max:20',
                Rule::unique('invoices', 'nomor_tagihan')->ignore($ignoreId),
            ],
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'periode' => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'jumlah' => ['required', 'integer', 'min:0', 'max:100000000000'],
            'jatuh_tempo' => ['required', 'date'],
            'tanggal_bayar' => ['nullable', 'date'],
            'status' => ['required', Rule::in(Invoice::STATUS)],
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
            'nomor_tagihan' => 'nomor tagihan',
            'tenant_id' => 'tenant',
            'periode' => 'periode',
            'jatuh_tempo' => 'jatuh tempo',
            'tanggal_bayar' => 'tanggal bayar',
        ];
    }

    /**
     * Pesan validasi khusus untuk format periode.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'periode.regex' => 'Periode harus berformat tahun-bulan, mis. 2026-11.',
        ];
    }

    /**
     * The invoice currently being edited, when the route model is already bound.
     */
    private function invoiceBeingUpdated(): ?Invoice
    {
        $invoice = $this->route('invoice');

        return $invoice instanceof Invoice ? $invoice : null;
    }
}
