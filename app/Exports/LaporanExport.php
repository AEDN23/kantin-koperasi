<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanExport implements WithEvents
{
    protected $laporans;
    protected float $grandTotal;
    protected string $periodeLabel;
    protected string $dicetakPada;

    public function __construct($laporans, float $grandTotal, string $periodeLabel, string $dicetakPada)
    {
        $this->laporans = $laporans;
        $this->grandTotal = $grandTotal;
        $this->periodeLabel = $periodeLabel;
        $this->dicetakPada = $dicetakPada;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setShowGridLines(true);

                // === 1. Judul Laporan ===
                $sheet->setCellValue('A1', 'LAPORAN BULANAN');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sheet->setCellValue('A2', $this->periodeLabel);
                $sheet->getStyle('A2')->getFont()->setSize(10);

                $sheet->setCellValue('A3', 'Dicetak pada');
                $sheet->setCellValue('B3', $this->dicetakPada);
                $sheet->getStyle('A3')->getFont()->setBold(true);

                // === 2. Header Tabel ===
                $headers = [
                    'A5' => 'No',
                    'B5' => 'Nama Karyawan',
                    'C5' => 'Line',
                    'D5' => 'Jml Trx',
                    'E5' => 'Total Piutang',
                    'F5' => 'Total Belanja',
                ];

                foreach ($headers as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                }

                $sheet->getStyle('A5:F5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF343A40'], // Dark header
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFB2B2B2'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // === 3. Data Laporan ===
                $currentRow = 6;
                $no = 1;

                foreach ($this->laporans as $index => $karyawan) {
                    $isColored = ($index % 2 === 0);

                    $sheet->setCellValue('A' . $currentRow, $no++);
                    $sheet->setCellValue('B' . $currentRow, $karyawan->nama_karyawan);
                    $sheet->setCellValue('C' . $currentRow, $karyawan->jabatan->nama_jabatan ?? '-');
                    $sheet->setCellValue('D' . $currentRow, $karyawan->jumlah_transaksi . 'x');
                    $sheet->setCellValue('E' . $currentRow, 'Rp ' . number_format($karyawan->total_piutang, 0, ',', '.'));
                    $sheet->setCellValue('F' . $currentRow, 'Rp ' . number_format($karyawan->total_belanja, 0, ',', '.'));

                    if ($isColored) {
                        $sheet->getStyle("A{$currentRow}:F{$currentRow}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFBDD7EE'); // Biru muda
                    }

                    $currentRow++;
                }

                $lastDataRow = $currentRow - 1;

                // Border untuk seluruh data
                if ($lastDataRow >= 6) {
                    $sheet->getStyle("A6:F{$lastDataRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FFB2B2B2'],
                            ],
                        ],
                    ]);

                    // Alignment
                    $sheet->getStyle("A5:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B5:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("D5:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E5:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                // === 4. Grand Total ===
                $sheet->setCellValue('D' . $currentRow, 'Total:');
                $sheet->setCellValue('E' . $currentRow, 'Rp ' . number_format($this->laporans->sum('total_piutang'), 0, ',', '.'));
                $sheet->setCellValue('F' . $currentRow, 'Rp ' . number_format($this->grandTotal, 0, ',', '.'));

                $sheet->getStyle("A{$currentRow}:F{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFF3CD'], // Warning yellow
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFB2B2B2'],
                        ],
                    ],
                ]);
                $sheet->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("E{$currentRow}:F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Auto size semua kolom
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
