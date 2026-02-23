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
        Schema::table('tambah_stoks', function (Blueprint $table) {
            if (!Schema::hasColumn('tambah_stoks', 'harga_beli')) {
                $table->integer('harga_beli')->default(0)->after('jumlah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tambah_stoks', function (Blueprint $table) {
            if (Schema::hasColumn('tambah_stoks', 'harga_beli')) {
                $table->dropColumn('harga_beli');
            }
        });
    }
};
