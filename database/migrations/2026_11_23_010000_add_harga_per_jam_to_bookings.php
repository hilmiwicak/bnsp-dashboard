<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tarif ruang dibekukan pada saat pemesanan dicatat, sehingga perubahan
     * harga ruang di kemudian hari tidak mengubah nilai pemesanan lama.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('harga_per_jam')
                ->nullable()
                ->after('room_id')
                ->comment('Tarif ruang saat pemesanan dicatat, rupiah per jam');
        });

        // Isi data lama dari tarif ruang yang berlaku sekarang.
        foreach (DB::table('rooms')->pluck('harga_per_jam', 'id') as $roomId => $rate) {
            DB::table('bookings')
                ->where('room_id', $roomId)
                ->update(['harga_per_jam' => $rate]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('harga_per_jam');
        });
    }
};
