<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Karyawan;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat 100 transaksi
        Transaksi::factory()->count(100)->create()->each(function ($transaksi) {
            // Set random date for transaction
            $randomDate = fake()->dateTimeBetween('-1 year', 'now');
            $transaksi->update([
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);

            // Untuk setiap transaksi, buat 1-5 detail transaksi
            $barangIds = Barang::pluck('id')->toArray();

            if (empty($barangIds)) {
                Barang::factory()->count(10)->create();
                $barangIds = Barang::pluck('id')->toArray();
            }

            $count = rand(1, 5);
            $selectedBarangs = (array) array_rand(array_flip($barangIds), $count);

            foreach ($selectedBarangs as $barangId) {
                $barang = Barang::find($barangId);
                $jumlah = rand(1, 3);

                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $barang->harga_jual,
                    'total_harga' => $jumlah * $barang->harga_jual,
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);
            }
        });
    }
}
