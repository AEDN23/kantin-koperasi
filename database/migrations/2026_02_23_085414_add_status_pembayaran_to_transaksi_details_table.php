<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi_details', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_details', 'status_pembayaran')) {
                $table->string('status_pembayaran')->default('belum_lunas')->after('metode_pembayaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_details', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_details', 'status_pembayaran')) {
                $table->dropColumn('status_pembayaran');
            }
        });
    }
};
