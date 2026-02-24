<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class BarangImport implements ToModel, WithStartRow, WithCalculatedFormulas
{
    public $successCount = 0;
    public $duplicateCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip jika nama barang kosong
        if (!isset($row[0]) || empty($row[0])) {
            return null;
        }

        $namaBarang = trim($row[0]);
        // Pastikan harga dan stok adalah angka (handle jika ada formula/string)
        $hargaBeli = (int) ($row[1] ?? 0);
        $hargaJual = (int) ($row[2] ?? 0);
        $stokAwal = (int) ($row[3] ?? 0);
        $stokMin = (int) ($row[4] ?? 5);

        // Jika sudah ada nama barang, SKIP (sesuai permintaan user)
        $exists = Barang::where('nama_barang', 'LIKE', $namaBarang)->exists();
        if ($exists) {
            $this->duplicateCount++;
            return null;
        }

        // Generate Kode Barang Otomatis
        $count = Barang::count() + 1;
        $kodeBarang = 'BRG-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        while (Barang::where('kode_barang', $kodeBarang)->exists()) {
            $count++;
            $kodeBarang = 'BRG-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $this->successCount++;

        return new Barang([
            'kode_barang' => $kodeBarang,
            'nama_barang' => $namaBarang,
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaJual,
            'stok' => $stokAwal,
            'stok_minimal' => $stokMin,
            'kategori_id' => null,
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}
