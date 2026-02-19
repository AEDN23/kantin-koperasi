@extends('layouts.app')

@section('title', 'Detail Transaksi')

@push('styles')
    <style>
        @media print {

            .sidebar,
            .no-print,
            .btn {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
                margin-bottom: 1rem !important;
            }

            body {
                background-color: white !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            main {
                padding: 0 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Detail Transaksi</h2>
        <a href="{{ route('transaksi.riwayat') }}" class="btn btn-secondary no-print">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th width="200">Kode Transaksi</th>
                    <td><code>{{ $transaksi->kode_transaksi }}</code></td>
                </tr>
                <tr>
                    <th>Karyawan</th>
                    <td>{{ $transaksi->karyawan->nama_karyawan ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td>{{ $transaksi->keterangan ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Item Belanja</h4>
        <button onclick="window.print()" class="btn btn-sm btn-outline-primary no-print">
            <i class="bi bi-printer"></i> Cetak Dokumen
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->transaksiDetails as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>Rp {{ number_format($detail->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-warning fw-bold">
                        <td colspan="4" class="text-end">Total Belanja:</td>
                        <td>Rp {{ number_format($transaksi->total_belanja, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection