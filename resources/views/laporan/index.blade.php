@extends('layouts.app')

@section('title', $isKaryawan ? 'Laporan Transaksi Saya' : 'Laporan Bulanan')

@section('content')
    <h2 class="mb-4">{{ $isKaryawan ? 'Laporan Transaksi Saya' : 'Laporan Bulanan' }}</h2>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="{{ $isKaryawan ? 'col-md-3' : 'col-md-3' }}">
                    <label for="dari" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="dari" name="dari" value="{{ $dari }}">
                </div>
                <div class="{{ $isKaryawan ? 'col-md-3' : 'col-md-3' }}">
                    <label for="sampai" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="sampai" name="sampai" value="{{ $sampai }}">
                </div>
                @if(!$isKaryawan)
                    <div class="col-md-3">
                        <label for="departemen_id" class="form-label">Departemen</label>
                        <select class="form-select" id="departemen_id" name="departemen_id">
                            <option value="">-- Semua Departemen --</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}" {{ $departemen_id == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="{{ $isKaryawan ? 'col-md-6' : 'col-md-3' }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                    @if($laporans->count() > 0)
                        <a href="{{ route('laporan.export', ['dari' => $dari, 'sampai' => $sampai, 'departemen_id' => $departemen_id]) }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari data...">
            </div>
            <table class="table table-hover searchable-table">
                <thead class="table-dark">
                    <tr>
                        <th data-orderable="false" style="width: 50px;">No</th>
                        <th>Nama Karyawan</th>
                        <th>Line</th>
                        <th>Jml Trx</th>
                        <th>Total Piutang</th>
                        <th>Total Belanja</th>
                        @if(!$isKaryawan)
                            <th>Est. Profit</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $index => $karyawan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if(!$isKaryawan)
                                    <a href="{{ route('karyawan.show', $karyawan) }}" class="fw-bold text-decoration-none">
                                        {{ $karyawan->nama_karyawan }}
                                    </a>
                                @else
                                    <span class="fw-bold">{{ $karyawan->nama_karyawan }}</span>
                                @endif
                            </td>
                            <td>{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                            <td>{{ $karyawan->jumlah_transaksi }}x</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($karyawan->total_piutang, 0, ',', '.') }}</td>
                            <td class="fw-bold">Rp {{ number_format($karyawan->total_belanja, 0, ',', '.') }}</td>
                            @if(!$isKaryawan)
                                <td class="text-success">Rp {{ number_format($karyawan->total_profit, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isKaryawan ? '6' : '7' }}" class="text-center text-muted py-4">Tidak ada transaksi di periode ini</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($laporans->count() > 0)
                    <tfoot>
                        <tr class="table-warning fw-bold">
                            <td colspan="4" class="text-end">Grand Total:</td>
                            <td class="text-danger">Rp {{ number_format($laporans->sum('total_piutang'), 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                            @if(!$isKaryawan)
                                <td class="text-success">Rp {{ number_format($laporans->sum('total_profit'), 0, ',', '.') }}</td>
                            @endif
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection