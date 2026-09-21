<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BarangExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function collection()
    {
        return Barang::with(['kategori', 'tambahStoks'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE BARANG',
            'NAMA BARANG',
            'QR / BARCODE',
            'KATEGORI',
            'HARGA BELI LAMA',
            'HARGA BELI TERBARU',
            'HARGA BELI RATA-RATA',
            'HARGA JUAL',
            'STOK',
            'STOK MINIMAL',
            'STATUS STOK',
        ];
    }

    public function map($barang): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->qr_code ?? '-',
            $barang->kategori->nama_kategori ?? '-',
            $barang->harga_beli_lama,
            $barang->harga_beli_terbaru,
            $barang->harga_beli_rata_rata,
            $barang->harga_jual,
            $barang->stok,
            $barang->stok_minimal,
            $barang->stok <= $barang->stok_minimal ? 'Menipis' : 'Aman',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
