@extends('layouts.app')

@section('title', 'Data Departemen')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Departemen</h2>
        <a href="{{ route('departemen.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Departemen
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
                        <th>Nama Departemen</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Karyawan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departemens as $index => $departemen)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $departemen->nama_departemen }}</td>
                            <td>{{ $departemen->deskripsi ?? '-' }}</td>
                            <td><span class="badge bg-primary">{{ $departemen->karyawans->count() }}</span></td>
                            <td>
                                <a href="{{ route('departemen.edit', $departemen) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('departemen.destroy', $departemen) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data departemen</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection