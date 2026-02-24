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
        $kategoriSembako = Kategori::where('nama_kategori', 'Sembako')->first();
        $kategoriMinuman = Kategori::where('nama_kategori', 'Minuman')->first();
        $kategoriSnack = Kategori::where('nama_kategori', 'Makanan Ringan')->first();
        $kategoriATK = Kategori::where('nama_kategori', 'Alat Tulis Kantor')->first();
        $kategoriMandi = Kategori::where('nama_kategori', 'Peralatan Mandi')->first();

        $barangs = [
            // Sembako
            [
                'nama_barang' => 'Beras Pandan Wangi 5kg',
                'harga_beli' => 65000,
                'harga_jual' => 75000,
                'stok' => 50,
                'stok_minimal' => 10,
                'kategori_id' => $kategoriSembako->id,
            ],
            [
                'nama_barang' => 'Minyak Goreng Bimoli 1L',
                'harga_beli' => 18000,
                'harga_jual' => 21000,
                'stok' => 100,
                'stok_minimal' => 20,
                'kategori_id' => $kategoriSembako->id,
            ],
            // Minuman
            [
                'nama_barang' => 'Aqua 600ml',
                'harga_beli' => 2500,
                'harga_jual' => 4000,
                'stok' => 200,
                'stok_minimal' => 50,
                'kategori_id' => $kategoriMinuman->id,
            ],
            [
                'nama_barang' => 'Teh Botol Sosro 450ml',
                'harga_beli' => 4500,
                'harga_jual' => 6000,
                'stok' => 48,
                'stok_minimal' => 12,
                'kategori_id' => $kategoriMinuman->id,
            ],
            // Snack
            [
                'nama_barang' => 'Indomie Goreng Spasial',
                'harga_beli' => 2800,
                'harga_jual' => 3500,
                'stok' => 300,
                'stok_minimal' => 40,
                'kategori_id' => $kategoriSnack->id,
            ],
            [
                'nama_barang' => 'Chitato Sapi Panggang',
                'harga_beli' => 8500,
                'harga_jual' => 11000,
                'stok' => 60,
                'stok_minimal' => 10,
                'kategori_id' => $kategoriSnack->id,
            ],
            // ATK
            [
                'nama_barang' => 'Buku Tulis Sinar Dunia 38 Lembar',
                'harga_beli' => 3000,
                'harga_jual' => 4500,
                'stok' => 120,
                'stok_minimal' => 20,
                'kategori_id' => $kategoriATK->id,
            ],
            [
                'nama_barang' => 'Pulpen Snowman V-1',
                'harga_beli' => 2000,
                'harga_jual' => 3000,
                'stok' => 150,
                'stok_minimal' => 30,
                'kategori_id' => $kategoriATK->id,
            ],
            // Mandi
            [
                'nama_barang' => 'Sabun Lifebuoy 80g',
                'harga_beli' => 3500,
                'harga_jual' => 5000,
                'stok' => 80,
                'stok_minimal' => 15,
                'kategori_id' => $kategoriMandi->id,
            ],
            [
                'nama_barang' => 'Shampoo Clear 160ml',
                'harga_beli' => 22000,
                'harga_jual' => 28000,
                'stok' => 40,
                'stok_minimal' => 5,
                'kategori_id' => $kategoriMandi->id,
            ],
        ];

        foreach ($barangs as $barang) {
            Barang::updateOrCreate(['nama_barang' => $barang['nama_barang']], $barang);
        }
    }
}
