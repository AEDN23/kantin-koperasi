@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Karyawan</h2>
        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th width="200">NIP</th>
                    <td>{{ $karyawan->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td>{{ $karyawan->nama_karyawan }}</td>
                </tr>
                <tr>
                    <th>Departemen</th>
                    <td>{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $karyawan->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <th>No HP</th>
                    <td>{{ $karyawan->no_hp ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $karyawan->email ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <h4>Riwayat Transaksi</h4>
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Total Belanja</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan->transaksis as $transaksi)
                        <tr>
                            <td><a href="{{ route('transaksi.show', $transaksi) }}">{{ $transaksi->kode_transaksi }}</a></td>
                            <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                            <td>Rp {{ number_format($transaksi->total_belanja, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection