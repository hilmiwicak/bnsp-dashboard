<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruang', 20)->unique();
            $table->string('nama_ruang');
            $table->enum('tipe', ['Meeting Room', 'Private Office', 'Virtual Office', 'Coworking Space']);
            $table->unsignedSmallInteger('kapasitas')->comment('Jumlah orang yang dapat ditampung');
            $table->string('lokasi');
            $table->unsignedInteger('harga_per_jam')->default(0)->comment('Tarif sewa per jam dalam rupiah');
            $table->text('fasilitas')->nullable();
            $table->enum('status', ['Tersedia', 'Terpakai', 'Perbaikan'])->default('Tersedia');
            $table->timestamps();

            $table->index('status');
            $table->index('tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
