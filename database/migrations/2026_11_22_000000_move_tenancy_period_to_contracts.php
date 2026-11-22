<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kontrak kini memegang seluruh periode sewa, termasuk pengajuan yang
        // belum diverifikasi, sehingga tenant tidak lagi menyimpan periode.
        Schema::table('contracts', function (Blueprint $table) {
            $table->enum('status', ['Aktif', 'Menunggu Verifikasi', 'Berakhir', 'Dibatalkan'])
                ->default('Aktif')
                ->change();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai_sewa', 'durasi_sewa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->date('tanggal_mulai_sewa')->nullable()->after('email');
            $table->unsignedSmallInteger('durasi_sewa')->default(12)->after('tanggal_mulai_sewa');
        });

        DB::table('contracts')
            ->where('status', 'Menunggu Verifikasi')
            ->update(['status' => 'Aktif']);

        Schema::table('contracts', function (Blueprint $table) {
            $table->enum('status', ['Aktif', 'Berakhir', 'Dibatalkan'])
                ->default('Aktif')
                ->change();
        });
    }
};
