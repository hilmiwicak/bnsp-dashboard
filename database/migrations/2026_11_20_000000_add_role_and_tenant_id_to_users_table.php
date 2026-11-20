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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'tenant', 'customer'])
                ->default('customer')
                ->after('password');

            // Satu akun hanya mewakili satu PIC dari satu perusahaan tenant,
            // jadi relasinya banyak-ke-satu (beberapa PIC boleh satu tenant).
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('role')
                ->constrained('tenants')
                ->nullOnDelete();
        });

        // Akun yang sudah ada sebelum role diperkenalkan adalah akun operator.
        DB::table('users')->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn('role');
        });
    }
};
