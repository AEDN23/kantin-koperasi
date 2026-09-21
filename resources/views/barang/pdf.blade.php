<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Data Barang - Warung Koperasi</title>
    <style>
        @page {
            margin: 1.2cm 1cm 1.5cm 1cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a5f;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin: 0 0 4px 0;
        }

        .header .meta {
            font-size: 9px;
            color: #777;
        }

        /* Summary Stats */
        .summary-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .summary-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 10px;
            text-align: center;
        }

        .summary-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }

        .summary-value {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 2px;
        }

        /* Data Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.data-table th, 
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            vertical-align: middle;
        }

        table.data-table th {
            background-color: #1e3a5f;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 0.5px;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .badge-danger {
            color: #b91c1c;
            background-color: #fee2e2;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-success {
            color: #15803d;
            background-color: #dcfce7;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .code-pill {
            font-family: 'Courier New', Courier, monospace;
            background-color: #e2e8f0;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8.5px;
        }

        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>🏪 Warung Koperasi</h1>
        <h2>Laporan Data & Stok Barang</h2>
        <div class="meta">
            Tanggal Cetak: {{ $tanggal }} WIB &bull; Sistem Informasi Warung Koperasi
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <table class="summary-table">
        <tr>
            <td style="width: 25%; padding-right: 5px;">
                <div class="summary-box">
                    <div class="summary-label">Total Item Barang</div>
                    <div class="summary-value">{{ number_format($barangs->count(), 0, ',', '.') }} Jenis</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 5px; padding-left: 5px;">
                <div class="summary-box">
                    <div class="summary-label">Total Unit Stok</div>
                    <div class="summary-value">{{ number_format($barangs->sum('stok'), 0, ',', '.') }} Pcs</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 5px; padding-left: 5px;">
                <div class="summary-box">
                    <div class="summary-label">Total Nilai Aset Beli</div>
                    <div class="summary-value">Rp {{ number_format($barangs->sum(fn($b) => $b->harga_beli_rata_rata * $b->stok), 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-left: 5px;">
                <div class="summary-box">
                    <div class="summary-label">Estimasi Nilai Jual</div>
                    <div class="summary-value">Rp {{ number_format($barangs->sum(fn($b) => $b->harga_jual * $b->stok), 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Barang -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 75px;">Kode</th>
                <th>Nama Barang</th>
                <th style="width: 75px;">Barcode / QR</th>
                <th style="width: 75px;">Kategori</th>
                <th style="width: 70px;">Beli Lama</th>
                <th style="width: 70px;">Beli Baru</th>
                <th style="width: 70px;">Beli Rata²</th>
                <th style="width: 70px;">Harga Jual</th>
                <th style="width: 40px;">Stok</th>
                <th style="width: 55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangs as $index => $barang)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $barang->kode_barang ?? '-' }}</td>
                    <td class="text-left fw-bold">{{ $barang->nama_barang }}</td>
                    <td class="text-center">
                        @if($barang->qr_code)
                            <span class="code-pill">{{ $barang->qr_code }}</span>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($barang->harga_beli_lama, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($barang->harga_beli_terbaru, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($barang->harga_beli_rata_rata, 0, ',', '.') }}</td>
                    <td class="text-right fw-bold">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ $barang->stok }}</td>
                    <td class="text-center">
                        @if($barang->stok <= $barang->stok_minimal)
                            <span class="badge-danger">Menipis</span>
                        @else
                            <span class="badge-success">Aman</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data barang yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($barangs->count() > 0)
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="9" class="text-right" style="padding: 6px;">Total Keseluruhan Stok:</td>
                    <td class="text-center" style="padding: 6px;">{{ number_format($barangs->sum('stok'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Warung Koperasi &bull; Dokumen Resmi
    </div>

</body>
</html>
