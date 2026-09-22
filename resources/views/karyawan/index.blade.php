@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Karyawan</h2>
        <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Karyawan
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
                        <th>NIP</th>
                        <th>Nama Karyawan</th>
                        <th>Departemen</th>
                        <th>Line</th>
                        <th class="text-end">Total Transaksi</th>
                        <th class="text-end">Total Piutang</th>
                        <th data-orderable="false" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $index => $karyawan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $karyawan->nip ?? '-' }}</td>
                            <td>{{ $karyawan->nama_karyawan }}</td>
                            <td>{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                            <td>{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                            <td class="text-end">
                                @if(($karyawan->transaksi_count ?? 0) > 0)
                                    <div class="fw-bold text-dark">Rp {{ number_format($karyawan->total_transaksi, 0, ',', '.') }}</div>
                                    <small class="text-muted">{{ $karyawan->transaksi_count }} transaksi</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(($karyawan->total_piutang ?? 0) > 0)
                                    <span class="badge bg-danger">Rp {{ number_format($karyawan->total_piutang, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i> Rp 0 (Lunas)</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('karyawan.show', $karyawan) }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('karyawan.edit', $karyawan) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('karyawan.destroy', $karyawan) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data karyawan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection