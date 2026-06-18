<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Karyawan;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawans = Karyawan::all();
        $barangs = Barang::all();

        if ($karyawans->isEmpty() || $barangs->isEmpty()) {
            $this->command->warn('Karyawan atau Barang kosong. Lewati seeder transaksi.');
            return;
        }

        $this->command->info('Memulai seeding 1000 transaksi (1 per hari)...');

        // Loop untuk 1000 hari ke belakang
        for ($i = 0; $i < 1000; $i++) {
            $date = Carbon::now()->subDays($i);

            // Buat Transaksi Utama
            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . $date->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'karyawan_id' => $karyawans->random()->id,
                'keterangan' => 'Test Seeder Day ' . $i,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Ambil 1-3 barang acak untuk detailnya
            $randomItems = $barangs->random(rand(1, 3));

            foreach ($randomItems as $barang) {
                $jumlah = rand(1, 5);
                $metode = rand(0, 1) ? 'piutang' : 'tunai';
                $status = ($metode == 'piutang' && rand(0, 1)) ? 'belum_lunas' : 'lunas';

                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $barang->harga_jual,
                    'total_harga' => $jumlah * $barang->harga_jual,
                    'metode_pembayaran' => $metode,
                    'status_pembayaran' => $status,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            // Progress log setiap 100 data
            if ($i % 100 == 0) {
                $this->command->comment("Telah memproses $i hari...");
            }
        }

        $this->command->info('Seeding 1000 transaksi selesai!');
    }
}
