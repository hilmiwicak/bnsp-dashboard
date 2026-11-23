<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PortalBookingRequest extends FormRequest
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
            // Hanya ruang berstatus tersedia yang boleh diajukan tenant.
            'room_id' => [
                'required', 'integer',
                Rule::exists('rooms', 'id')->where('status', 'Tersedia'),
            ],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'jumlah_peserta' => ['required', 'integer', 'min:1', 'max:500'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Tolak jadwal yang bentrok dengan pemesanan lain pada ruang yang sama.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            // Tanggal harus berada di dalam periode sewa yang berlaku.
            if ($pesan = $this->periodErrorMessage()) {
                $validator->errors()->add('tanggal', $pesan);

                return;
            }

            $bentrok = Booking::query()
                ->where('room_id', $this->input('room_id'))
                ->overlapping(
                    (string) $this->input('tanggal'),
                    (string) $this->input('jam_mulai'),
                    (string) $this->input('jam_selesai'),
                )
                ->exists();

            if ($bentrok) {
                $validator->errors()->add(
                    'jam_mulai',
                    'Ruang ini sudah dipesan pada rentang jam tersebut.',
                );
            }
        });
    }

    /**
     * Pesan galat bila tanggal di luar periode sewa, atau null bila tidak ada
     * kontrak sama sekali. Tenant tanpa kontrak tidak dibatasi.
     */
    private function periodErrorMessage(): ?string
    {
        $contract = $this->user()?->tenant?->currentContract;

        if (! $contract) {
            return null;
        }

        $tanggal = Carbon::parse((string) $this->input('tanggal'));

        if ($tanggal->betweenIncluded($contract->tanggal_mulai, $contract->tanggal_berakhir)) {
            return null;
        }

        return 'Tanggal harus berada dalam periode sewa Anda ('
            .$contract->tanggal_mulai->format('d/m/Y').' – '
            .$contract->tanggal_berakhir->format('d/m/Y').').';
    }

    /**
     * Human readable attribute names used in validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'room_id' => 'ruang',
            'jam_mulai' => 'jam mulai',
            'jam_selesai' => 'jam selesai',
            'jumlah_peserta' => 'jumlah peserta',
        ];
    }
}
