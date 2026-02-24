@extends('layouts.app')

@section('title', 'Pelunasan Piutang')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Daftar Piutang Karyawan</h2>
            <p class="text-muted">Kelola pelunasan piutang atau pemotongan gaji</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari nama karyawan...">
            </div>
            <div class="table-responsive">
                <table class="table table-hover searchable-table align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Departemen</th>
                            <th class="text-end">Total Piutang</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawans as $index => $karyawan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $karyawan->nama_karyawan }}</div>
                                    <small class="text-muted">NIP: {{ $karyawan->nip }}</small>
                                </td>
                                <td>{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                                <td class="text-end fw-bold text-danger">
                                    Rp {{ number_format($karyawan->total_piutang, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('piutang.show', $karyawan) }}" class="btn btn-primary btn-sm px-3">
                                        <i class="bi bi-eye"></i> Detail & Pelunasan
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                    Tidak ada piutang yang belum lunas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection