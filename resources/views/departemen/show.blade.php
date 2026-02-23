@extends('layouts.app')

@section('title', 'Detail Departemen')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Departemen</h2>
        <a href="{{ route('departemen.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Info Departemen</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nama Departemen</th>
                            <td>: {{ $departemen->nama_departemen }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>: {{ $departemen->deskripsi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Total Karyawan</th>
                            <td>: <span class="badge bg-info">{{ $departemen->karyawans->count() }} orang</span></td>
                        </tr>
                        <tr>
                            <th>Total Belanja Dept.</th>
                            <td>: <span class="badge bg-success">Rp {{ number_format($transaksis->sum(fn($t) => $t->total_belanja), 0, ',', '.') }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Riwayat -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('departemen.show', $departemen) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('departemen.show', $departemen) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Riwayat Transaksi Departemen</h5>
            <span class="badge bg-light text-dark">{{ $transaksis->count() }} Transaksi Terdeteksi</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Karyawan</th>
                            <th>Total Belanja</th>
                            <th>Metode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksis as $index => $trx)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-secondary">{{ $trx->kode_transaksi }}</span></td>
                                <td>{{ $trx->karyawan->nama_karyawan ?? 'Hapus' }}</td>
                                <td>Rp {{ number_format($trx->total_belanja, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $metode = $trx->metode_pembayaran;
                                    @endphp
                                    <span class="badge {{ $metode == 'Tunai' ? 'bg-success' : ($metode == 'Piutang' ? 'bg-warning text-dark' : 'bg-info') }}">
                                        {{ $metode }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('transaksi.show', $trx) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail Transaksi">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada riwayat transaksi untuk departemen ini pada periode yang dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection