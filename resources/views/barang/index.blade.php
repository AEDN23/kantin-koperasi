@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Data Barang</h2>
            <p class="text-muted">Kelola stok dan informasi produk koperasi</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel"></i> Import Excel
            </button>
            <a href="{{ route('barang.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari data barang...">
            </div>
            <div class="table-responsive">
                <table class="table table-hover searchable-table align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th class="text-end">Harga Beli</th>
                            <th class="text-end">Harga Jual</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $index => $barang)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $barang->nama_barang }}</div>
                                    <small class="text-muted">{{ $barang->kode_barang }}</small>
                                </td>
                                <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($barang->stok <= $barang->stok_minimal)
                                        <span class="badge bg-danger p-2" title="Stok Menipis!">
                                            {{ $barang->stok }} <i class="bi bi-exclamation-triangle ms-1"></i>
                                        </span>
                                    @else
                                        <span class="badge bg-success p-2">
                                            {{ $barang->stok }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('barang.show', $barang) }}"
                                            class="btn btn-sm btn-info text-white shadow-sm" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('barang.edit', $barang) }}" class="btn btn-sm btn-warning shadow-sm"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('barang.destroy', $barang) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger shadow-sm" title="Hapus"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                                    Belum ada data barang. Silakan tambah atau import dari Excel.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('barang.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="importModalLabel"><i class="bi bi-file-earmark-excel me-2"></i>Import
                            Barang dari Excel</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6 class="fw-bold"><i class="bi bi-info-circle me-1"></i>Petunjuk Import:</h6>
                            <ul class="small mb-2">
                                <li>Gunakan template yang tersedia untuk menghindari kesalahan format.</li>
                                <li>Kolom <strong>NAMA BARANG, HARGA BELI, dan HARGA JUAL</strong> wajib diisi.</li>
                                <li><strong>Kategori</strong> akan dikosongkan secara default (NULL).</li>
                            </ul>
                            <a href="{{ route('barang.download-template') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> Download Template Excel
                            </a>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label fw-bold">Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-cloud-upload me-1"></i> Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection