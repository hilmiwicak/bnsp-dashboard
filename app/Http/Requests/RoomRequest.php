<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
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
        $ignoreId = $this->roomBeingUpdated()?->id;

        return [
            'kode_ruang' => [
                'required', 'string', 'max:20',
                Rule::unique('rooms', 'kode_ruang')->ignore($ignoreId),
            ],
            'nama_ruang' => ['required', 'string', 'max:255'],
            'tipe' => ['required', Rule::in(Room::TIPE)],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:500'],
            'lokasi' => ['required', 'string', 'max:255'],
            'harga_per_jam' => ['required', 'integer', 'min:0', 'max:100000000'],
            'fasilitas' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(Room::STATUS)],
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
            'kode_ruang' => 'kode ruang',
            'nama_ruang' => 'nama ruang',
            'harga_per_jam' => 'harga per jam',
        ];
    }

    /**
     * The room currently being edited, when the route model is already bound.
     */
    private function roomBeingUpdated(): ?Room
    {
        $room = $this->route('room');

        return $room instanceof Room ? $room : null;
    }
}
