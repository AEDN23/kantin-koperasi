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
        $karyawans = Karyawan::all();
        $barangs = Barang::all();

        if ($karyawans->isEmpty() || $barangs->isEmpty()) {
            return;
        }

        $transactions = [
            [
                'kode_transaksi' => 'TRX-001',
                'karyawan_id' => $karyawans->where('nip', '1001')->first()->id ?? $karyawans->random()->id,
                'keterangan' => 'Pembelian rutin Bulanan',
                'details' => [
                    ['barang_nama' => 'Beras Pandan Wangi 5kg', 'jumlah' => 1, 'metode' => 'piutang'],
                    ['barang_nama' => 'Minyak Goreng Bimoli 1L', 'jumlah' => 2, 'metode' => 'piutang'],
                ],
            ],
            [
                'kode_transaksi' => 'TRX-002',
                'karyawan_id' => $karyawans->where('nip', '1003')->first()->id ?? $karyawans->random()->id,
                'keterangan' => 'Cemilan kantor',
                'details' => [
                    ['barang_nama' => 'Indomie Goreng Spasial', 'jumlah' => 5, 'metode' => 'tunai'],
                    ['barang_nama' => 'Aqua 600ml', 'jumlah' => 10, 'metode' => 'tunai'],
                ],
            ],
            [
                'kode_transaksi' => 'TRX-003',
                'karyawan_id' => $karyawans->where('nip', '1004')->first()->id ?? $karyawans->random()->id,
                'keterangan' => 'Kebutuhan ATK',
                'details' => [
                    ['barang_nama' => 'Buku Tulis Sinar Dunia 38 Lembar', 'jumlah' => 2, 'metode' => 'piutang'],
                    ['barang_nama' => 'Pulpen Snowman V-1', 'jumlah' => 3, 'metode' => 'piutang'],
                ],
            ],
            [
                'kode_transaksi' => 'TRX-004',
                'karyawan_id' => $karyawans->where('nip', '1002')->first()->id ?? $karyawans->random()->id,
                'keterangan' => 'Belanja mingguan',
                'details' => [
                    ['barang_nama' => 'Sabun Lifebuoy 80g', 'jumlah' => 3, 'metode' => 'tunai'],
                    ['barang_nama' => 'Teh Botol Sosro 450ml', 'jumlah' => 2, 'metode' => 'tunai'],
                ],
            ],
        ];

        foreach ($transactions as $tData) {
            $transaksi = Transaksi::updateOrCreate(
                ['kode_transaksi' => $tData['kode_transaksi']],
                [
                    'karyawan_id' => $tData['karyawan_id'],
                    'keterangan' => $tData['keterangan'],
                ]
            );

            foreach ($tData['details'] as $dData) {
                $barang = Barang::where('nama_barang', $dData['barang_nama'])->first();
                if ($barang) {
                    TransaksiDetail::updateOrCreate(
                        [
                            'transaksi_id' => $transaksi->id,
                            'barang_id' => $barang->id,
                        ],
                        [
                            'jumlah' => $dData['jumlah'],
                            'harga_satuan' => $barang->harga_jual,
                            'total_harga' => $dData['jumlah'] * $barang->harga_jual,
                            'metode_pembayaran' => $dData['metode'],
                        ]
                    );
                }
            }
        }
    }
}
