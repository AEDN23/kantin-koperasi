<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BarangTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'NAMA BARANG',
            'HARGA BELI',
            'HARGA JUAL',
            'STOK AWAL',
            'STOK MINIMAL',
        ];
    }
}
