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
        Schema::create('penyewa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penyewa', 20)->unique();
            $table->string('nama_pic');
            $table->string('nama_perusahaan');
            $table->string('nib_npwp', 40)->nullable();
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->text('alamat');
            $table->string('no_hp', 20);
            $table->string('email')->unique();
            $table->enum('paket_layanan', [
                'Virtual Office',
                'Virtual Office Plus',
                'Private Office',
                'Ruang Meeting',
            ]);
            $table->date('tanggal_mulai_sewa');
            $table->unsignedSmallInteger('durasi_sewa')->comment('Durasi sewa dalam bulan');
            $table->enum('status', ['Aktif', 'Menunggu Verifikasi', 'Nonaktif'])
                ->default('Menunggu Verifikasi');
            $table->timestamps();

            $table->index(['paket_layanan', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewa');
    }
};
