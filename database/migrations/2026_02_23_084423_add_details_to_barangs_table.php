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
        Schema::table('barangs', function (Blueprint $table) {
            if (!Schema::hasColumn('barangs', 'harga_beli')) {
                $table->integer('harga_beli')->default(0)->after('nama_barang');
            }
            if (!Schema::hasColumn('barangs', 'stok_minimal')) {
                $table->integer('stok_minimal')->default(5)->after('stok');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (Schema::hasColumn('barangs', 'harga_beli')) {
                $table->dropColumn('harga_beli');
            }
            if (Schema::hasColumn('barangs', 'stok_minimal')) {
                $table->dropColumn('stok_minimal');
            }
        });
    }
};
