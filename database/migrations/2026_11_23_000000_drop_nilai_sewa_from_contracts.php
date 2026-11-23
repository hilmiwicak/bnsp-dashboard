<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nilai sewa tidak lagi disimpan di kontrak. Nilai uang hanya hidup di
     * tabel invoices, sehingga tidak ada dua sumber angka yang bisa berbeda.
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('nilai_sewa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('nilai_sewa')
                ->default(0)
                ->after('tanggal_berakhir')
                ->comment('Nilai sewa kontrak dalam rupiah');
        });
    }
};
