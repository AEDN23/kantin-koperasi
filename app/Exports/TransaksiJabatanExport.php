<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransaksiJabatanExport implements WithEvents
{
    protected $transaksis;
    protected string $namaJabatan;
    protected string $periodeLabel;
    protected string $dicetakPada;

    public function __construct($transaksis, string $namaJabatan, string $periodeLabel, string $dicetakPada)
    {
        $this->transaksis = $transaksis;
        $this->namaJabatan = $namaJabatan;
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
                $sheet->setCellValue('A1', 'LAPORAN RIWAYAT TRANSAKSI BERDASARKAN JABATAN');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

                $sheet->setCellValue('A2', 'Jabatan');
                $sheet->setCellValue('B2', $this->namaJabatan);
                $sheet->getStyle('A2')->getFont()->setBold(true);

                $sheet->setCellValue('A3', $this->periodeLabel);

                $sheet->setCellValue('A4', 'Dicetak pada');
                $sheet->setCellValue('B4', $this->dicetakPada);
                $sheet->getStyle('A4')->getFont()->setBold(true);

                // === 2. Header Tabel ===
                $headers = [
                    'A6' => 'No',
                    'B6' => 'Tanggal & Waktu',
                    'C6' => 'Kode Transaksi',
                    'D6' => 'Nama Karyawan',
                    'E6' => 'Barang yang Dibeli',
                    'F6' => 'Qty',
                    'G6' => 'Harga Satuan',
                    'H6' => 'Total Item',
                    'I6' => 'Total Transaksi',
                    'J6' => 'Metode Pembayaran',
                ];

                foreach ($headers as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                }

                $sheet->getStyle('A6:J6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFB2B2B2'],
                        ],
                    ],
                ]);

                // Helper: label metode pembayaran per-detail
                $metodeLabel = function ($detail) {
                    $metode = strtolower($detail->metode_pembayaran ?? '');
                    if ($metode === 'piutang') {
                        $status = strtolower($detail->status_pembayaran ?? '');
                        return 'Piutang' . ($status === 'lunas' ? ' (Lunas)' : ' (Belum Lunas)');
                    }
                    return ucfirst($detail->metode_pembayaran ?? '-');
                };

                $currentRow = 7;
                $no = 1;
                $grandTotal = 0;

                // === 3. Data Transaksi ===
                foreach ($this->transaksis as $trxIndex => $trx) {
                    $startRow = $currentRow;
                    $details = $trx->transaksiDetails;
                    $jumlahDetail = $details->count();
                    $isColored = ($trxIndex % 2 === 0); // Bergantian warna setiap ganti transaksi

                    if ($jumlahDetail === 0) {
                        $sheet->setCellValue('A' . $currentRow, $no++);
                        $sheet->setCellValue('B' . $currentRow, $trx->created_at->format('d/m/Y H:i') . ' WIB');
                        $sheet->setCellValue('C' . $currentRow, $trx->kode_transaksi);
                        $sheet->setCellValue('D' . $currentRow, $trx->karyawan->nama_karyawan ?? '-');
                        $sheet->setCellValue('E' . $currentRow, '-');
                        $sheet->setCellValue('F' . $currentRow, '-');
                        $sheet->setCellValue('G' . $currentRow, '-');
                        $sheet->setCellValue('H' . $currentRow, '-');
                        $sheet->setCellValue('I' . $currentRow, 'Rp. ' . number_format($trx->total_belanja, 0, ',', '.'));
                        $sheet->setCellValue('J' . $currentRow, $trx->metode_pembayaran);
                        $currentRow++;
                    } else {
                        foreach ($details as $i => $detail) {
                            if ($i === 0) {
                                $sheet->setCellValue('A' . $currentRow, $no++);
                                $sheet->setCellValue('B' . $currentRow, $trx->created_at->format('d/m/Y H:i') . ' WIB');
                                $sheet->setCellValue('C' . $currentRow, $trx->kode_transaksi);
                                $sheet->setCellValue('D' . $currentRow, $trx->karyawan->nama_karyawan ?? '-');
                                $sheet->setCellValue('I' . $currentRow, 'Rp. ' . number_format($trx->total_belanja, 0, ',', '.'));
                            } else {
                                $sheet->setCellValue('A' . $currentRow, '');
                                $sheet->setCellValue('B' . $currentRow, '');
                                $sheet->setCellValue('C' . $currentRow, '');
                                $sheet->setCellValue('D' . $currentRow, '');
                                $sheet->setCellValue('I' . $currentRow, '');
                            }

                            $sheet->setCellValue('E' . $currentRow, $detail->barang->nama_barang ?? 'Barang Terhapus');
                            $sheet->setCellValue('F' . $currentRow, $detail->jumlah);
                            $sheet->setCellValue('G' . $currentRow, 'Rp. ' . number_format($detail->harga_satuan, 0, ',', '.'));
                            $sheet->setCellValue('H' . $currentRow, 'Rp. ' . number_format($detail->total_harga, 0, ',', '.'));
                            $sheet->setCellValue('J' . $currentRow, $metodeLabel($detail));

                            $currentRow++;
                        }
                    }

                    $endRow = $currentRow - 1;
                    $grandTotal += $trx->total_belanja;

                    // Fill color bergantian untuk setiap transaksi (semua baris dalam transaksi tersebut)
                    if ($isColored) {
                        $sheet->getStyle("A{$startRow}:J{$endRow}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFBDD7EE'); // Biru muda lembut sesuai gambar
                    }
                }

                $lastDataRow = $currentRow - 1;

                // Border dan alignment untuk seluruh tabel data
                if ($lastDataRow >= 7) {
                    $sheet->getStyle("A7:J{$lastDataRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FFB2B2B2'],
                            ],
                        ],
                    ]);

                    // Alignment
                    $sheet->getStyle("A6:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B6:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C6:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D6:E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("F6:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G6:I{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("J6:J{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                // === 4. Baris Total Keseluruhan ===
                $currentRow++; // Baris kosong
                $sheet->setCellValue('H' . $currentRow, 'TOTAL KESELURUHAN');
                $sheet->setCellValue('I' . $currentRow, 'Rp. ' . number_format($grandTotal, 0, ',', '.'));
                $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getFont()->setBold(true);
                $sheet->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $currentRow += 2;
                $sheet->setCellValue('A' . $currentRow, 'Total Transaksi');
                $sheet->setCellValue('B' . $currentRow, count($this->transaksis) . ' transaksi');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);

                // Auto size semua kolom
                foreach (range('A', 'J') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
