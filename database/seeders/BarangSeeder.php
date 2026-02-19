<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada kategori sebelum membuat barang
        if (Kategori::count() === 0) {
            Kategori::factory()->count(5)->create();
        }

        Barang::factory()->count(20)->create();
    }
}
