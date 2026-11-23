<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Rules\FitsRoomCapacity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BookingRequest extends FormRequest
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
        $ignoreId = $this->bookingBeingUpdated()?->id;

        return [
            'kode_pemesanan' => [
                'required', 'string', 'max:20',
                Rule::unique('bookings', 'kode_pemesanan')->ignore($ignoreId),
            ],
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'jumlah_peserta' => [
                'required', 'integer', 'min:1', 'max:500',
                new FitsRoomCapacity($this->integer('room_id')),
            ],
            'status' => ['required', Rule::in(Booking::STATUS)],
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

            // Pemesanan yang ditolak tidak memakai slot ruang.
            if ($this->input('status') === 'Ditolak') {
                return;
            }

            $bentrok = Booking::query()
                ->where('room_id', $this->input('room_id'))
                ->overlapping(
                    (string) $this->input('tanggal'),
                    (string) $this->input('jam_mulai'),
                    (string) $this->input('jam_selesai'),
                    $this->bookingBeingUpdated()?->id,
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
     * Human readable attribute names used in validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kode_pemesanan' => 'kode pemesanan',
            'tenant_id' => 'tenant',
            'room_id' => 'ruang',
            'jam_mulai' => 'jam mulai',
            'jam_selesai' => 'jam selesai',
            'jumlah_peserta' => 'jumlah peserta',
        ];
    }

    /**
     * The booking currently being edited, when the route model is already bound.
     */
    private function bookingBeingUpdated(): ?Booking
    {
        $booking = $this->route('booking');

        return $booking instanceof Booking ? $booking : null;
    }
}
