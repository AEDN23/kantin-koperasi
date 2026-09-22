@extends('layouts.app')

@section('title', 'Data Line')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Line</h2>
        <a href="{{ route('jabatan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Line
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
                        <th data-orderable="false" style="width: 50px;">No</th>
                        <th>Nama Line</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Karyawan</th>
                        <th data-orderable="false" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jabatans as $index => $jabatan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="fw-semibold">{{ $jabatan->nama_jabatan }}</span></td>
                            <td>{{ $jabatan->deskripsi ?? '-' }}</td>
                            <td><span class="badge bg-primary">{{ $jabatan->karyawans_count }}</span></td>
                            <td>
                                <a href="{{ route('jabatan.show', $jabatan) }}" class="btn btn-sm btn-info text-white" title="Lihat Anggota">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('jabatan.edit', $jabatan) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('jabatan.destroy', $jabatan) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin hapus jabatan {{ $jabatan->nama_jabatan }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data jabatan</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($jabatans->count() > 0)
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3" class="text-end">Total Karyawan Berjabatan:</td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $jabatans->sum('karyawans_count') }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
