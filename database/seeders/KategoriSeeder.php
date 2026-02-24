<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Sembako', 'deskripsi' => 'Sembilan Bahan Pokok'],
            ['nama_kategori' => 'Minuman', 'deskripsi' => 'Aneka minuman segar'],
            ['nama_kategori' => 'Makanan Ringan', 'deskripsi' => 'Snack dan camilan'],
            ['nama_kategori' => 'Alat Tulis Kantor', 'deskripsi' => 'Peralatan sekolah dan kantor'],
            ['nama_kategori' => 'Peralatan Mandi', 'deskripsi' => 'Sabun, shampoo, dan lainnya'],
        ];

        foreach ($categories as $category) {
            Kategori::updateOrCreate(['nama_kategori' => $category['nama_kategori']], $category);
        }
    }
}
