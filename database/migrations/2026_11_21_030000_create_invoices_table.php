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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan', 20)->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('periode', 7)->comment('Periode tagihan dalam format YYYY-MM');
            $table->unsignedBigInteger('jumlah')->default(0)->comment('Jumlah tagihan dalam rupiah');
            $table->date('jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['Belum Dibayar', 'Lunas', 'Terlambat'])->default('Belum Dibayar');
            $table->timestamps();

            $table->index('status');
            $table->index(['tenant_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
