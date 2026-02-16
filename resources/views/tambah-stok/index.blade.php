@extends('layouts.app')

@section('title', 'Tambah Stok')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Tambah Stok</h2>
        <a href="{{ route('tambah-stok.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Stok
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tambahStoks as $index => $stok)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $stok->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $stok->barang->nama_barang ?? '-' }}</td>
                            <td><span class="badge bg-success">+{{ $stok->jumlah }}</span></td>
                            <td>{{ $stok->keterangan ?? '-' }}</td>
                            <td>
                                <a href="{{ route('tambah-stok.edit', $stok) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('tambah-stok.destroy', $stok) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin hapus? Stok barang akan dikurangi.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data stok masuk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection