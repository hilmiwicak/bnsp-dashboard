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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontrak', 20)->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->unsignedBigInteger('nilai_sewa')->default(0)->comment('Nilai sewa kontrak dalam rupiah');
            $table->enum('status', ['Aktif', 'Berakhir', 'Dibatalkan'])->default('Aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
