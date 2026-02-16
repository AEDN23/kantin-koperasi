@extends('layouts.app')

@section('title', 'Tambah Stok')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Tambah Stok</h2>
        <a href="{{ route('tambah-stok.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Stok
        </a>
    </div>

    {{-- Filter Tanggal --}}
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('tambah-stok.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="dari" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="dari" name="dari" value="{{ request('dari') }}">
                </div>
                <div class="col-md-3">
                    <label for="sampai" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="sampai" name="sampai" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Urutkan</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('tambah-stok.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>
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