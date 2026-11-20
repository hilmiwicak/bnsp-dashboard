<x-layouts.dashboard title="Edit Ruang" heading="Edit Data Ruang">
    <div class="mx-auto max-w-[980px]">
        <p class="mb-6 text-[15px] text-dash-muted">
            Perbarui data ruang <span class="font-medium text-dash-ink">{{ $room->nama_ruang }}</span>
            ({{ $room->kode_ruang }}). Kolom bertanda
            <span class="text-dash-alert">*</span> wajib diisi.
        </p>

        @include('rooms.form', [
            'action' => route('ruang-fasilitas.update', $room),
            'method' => 'PUT',
            'submitLabel' => 'Perbarui Data Ruang',
        ])
    </div>
</x-layouts.dashboard>
