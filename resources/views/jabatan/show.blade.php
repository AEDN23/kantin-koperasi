@extends('layouts.app')

@section('title', 'Detail Jabatan - ' . $jabatan->nama_jabatan)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Detail Jabatan</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('jabatan.index') }}">Jabatan</a></li>
                    <li class="breadcrumb-item active">{{ $jabatan->nama_jabatan }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Info Jabatan --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-briefcase-fill me-2"></i>Info Jabatan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="180" class="text-muted fw-semibold">Nama Jabatan</th>
                            <td><strong>{{ $jabatan->nama_jabatan }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold">Deskripsi</th>
                            <td>{{ $jabatan->deskripsi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-3 text-center bg-light">
                                <div class="fs-3 fw-bold text-primary">{{ $jabatan->karyawans->count() }}</div>
                                <div class="text-muted small">Total Karyawan</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center bg-light">
                                <div class="fs-6 fw-bold text-success">
                                    Rp {{ number_format($transaksis->sum(fn($t) => $t->total_belanja), 0, ',', '.') }}
                                </div>
                                <div class="text-muted small">Total Belanja</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center bg-light">
                                <div class="fs-3 fw-bold text-info">{{ $transaksis->count() }}</div>
                                <div class="text-muted small">Transaksi (periode ini)</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center bg-light">
                                @php
                                    $avg = $transaksis->count() > 0
                                        ? $transaksis->avg(fn($t) => $t->total_belanja)
                                        : 0;
                                @endphp
                                <div class="fs-6 fw-bold text-warning">
                                    Rp {{ number_format($avg, 0, ',', '.') }}
                                </div>
                                <div class="text-muted small">Rata-rata / Transaksi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Export --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('jabatan.show', $jabatan) }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('jabatan.show', $jabatan) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                        <a href="{{ route('jabatan.export-excel', array_merge(['jabatan' => $jabatan->id], request()->only(['dari', 'sampai']))) }}"
                           class="btn btn-success ms-auto">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Karyawan --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-people-fill me-2"></i>Daftar Karyawan</h6>
            <span class="badge bg-light text-dark">{{ $jabatan->karyawans->count() }} orang</span>
        </div>
        <div class="card-body p-0">
            @if($jabatan->karyawans->isEmpty())
                <p class="text-muted text-center py-3">Belum ada karyawan dengan jabatan ini.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Nama Karyawan</th>
                                <th>Departemen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jabatan->karyawans as $i => $kar)
                                <tr>
                                    <td class="ps-3">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                                 style="width:32px;height:32px;font-size:.8rem;flex-shrink:0;">
                                                {{ strtoupper(substr($kar->nama_karyawan, 0, 1)) }}
                                            </div>
                                            {{ $kar->nama_karyawan }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $kar->departemen->nama_departemen ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('karyawan.show', $kar) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Riwayat Transaksi --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi Jabatan</h5>
            <div class="d-flex align-items-center gap-2">
                @if(request('dari') || request('sampai'))
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-calendar-range"></i>
                        {{ request('dari') ? \Carbon\Carbon::parse(request('dari'))->format('d/m/Y') : '...' }}
                        &ndash;
                        {{ request('sampai') ? \Carbon\Carbon::parse(request('sampai'))->format('d/m/Y') : '...' }}
                    </span>
                @endif
                <span class="badge bg-light text-dark">{{ $transaksis->count() }} transaksi</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelRiwayat">
                    <thead class="table-secondary">
                        <tr>
                            <th class="ps-3">No</th>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Karyawan</th>
                            <th>Barang Dibeli</th>
                            <th class="text-end">Total Belanja</th>
                            <th>Metode</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksis as $index => $trx)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $trx->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $trx->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary font-monospace">{{ $trx->kode_transaksi }}</span>
                                </td>
                                <td>{{ $trx->karyawan->nama_karyawan ?? '-' }}</td>
                                <td>
                                    @if($trx->transaksiDetails->isEmpty())
                                        <span class="text-muted">-</span>
                                    @else
                                        <ul class="mb-0 ps-3 small">
                                            @foreach($trx->transaksiDetails as $detail)
                                                <li>
                                                    {{ $detail->barang->nama_barang ?? 'Barang Terhapus' }}
                                                    <span class="text-muted">({{ $detail->jumlah }}x)</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($trx->total_belanja, 0, ',', '.') }}
                                </td>
                                <td>
                                    @php $metode = $trx->metode_pembayaran; @endphp
                                    <span class="badge {{ $metode === 'Tunai' ? 'bg-success' : ($metode === 'Piutang' ? 'bg-warning text-dark' : 'bg-info text-dark') }}">
                                        {{ $metode }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('transaksi.show', $trx) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada riwayat transaksi untuk jabatan ini
                                    {{ (request('dari') || request('sampai')) ? 'pada periode yang dipilih.' : '.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($transaksis->count() > 0)
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="5" class="text-end pe-3 ps-3">Total Keseluruhan</td>
                                <td class="text-end text-success fw-bold">
                                    Rp {{ number_format($transaksis->sum(fn($t) => $t->total_belanja), 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
