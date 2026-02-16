@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Riwayat Transaksi</h2>
        <a href="{{ route('transaksi.index') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Transaksi Baru
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari data...">
            </div>
            <table class="table table-hover searchable-table">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Karyawan</th>
                        <th>Jumlah Item</th>
                        <th>Total Belanja</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $index => $transaksi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><code>{{ $transaksi->kode_transaksi }}</code></td>
                            <td>{{ $transaksi->karyawan->nama_karyawan ?? '-' }}</td>
                            <td><span class="badge bg-info">{{ $transaksi->transaksiDetails->count() }} item</span></td>
                            <td>Rp {{ number_format($transaksi->total_belanja, 0, ',', '.') }}</td>
                            <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('transaksi.show', $transaksi) }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('transaksi.destroy', $transaksi) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin hapus transaksi ini? Stok barang akan dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection