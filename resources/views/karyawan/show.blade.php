@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Karyawan</h2>
        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th width="200">NIP</th>
                    <td>{{ $karyawan->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td>{{ $karyawan->nama_karyawan }}</td>
                </tr>
                <tr>
                    <th>Departemen</th>
                    <td>{{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $karyawan->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <th>No HP</th>
                    <td>{{ $karyawan->no_hp ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $karyawan->email ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <h4>Riwayat Transaksi</h4>

    {{-- Filter Tanggal --}}
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('karyawan.show', $karyawan) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="dari" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="dari" name="dari" value="{{ request('dari') }}">
                </div>
                <div class="col-md-3">
                    <label for="sampai" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="sampai" name="sampai" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">Urutkan</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('karyawan.show', $karyawan) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                    @if($karyawan->transaksis->count() > 0)
                        <button type="button" class="btn btn-success" onclick="exportExcel()">
                            <i class="bi bi-file-earmark-excel"></i> Export
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover" id="tabelRiwayat">
                <thead class="table-dark">
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Quantity</th>
                        <th>Total Belanja</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan->transaksis as $transaksi)
                        <tr>
                            <td><a href="{{ route('transaksi.show', $transaksi) }}">{{ $transaksi->kode_transaksi }}</a></td>
                            <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    @foreach($transaksi->transaksiDetails as $detail)
                                        <li>{{ $detail->barang->nama_barang ?? 'Barang Terhapus' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class=" mb-0 ps-3">
                                    @foreach($transaksi->transaksiDetails as $detail)
                                        <li>{{ $detail->jumlah }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>Rp {{ number_format($transaksi->total_belanja, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($karyawan->transaksis->count() > 0)
                    <tfoot>
                        <tr class="table-warning fw-bold">
                            <td colspan="4" class="text-end">Total Semua Transaksi:</td>
                            <td>Rp {{ number_format($karyawan->transaksis->sum('total_belanja'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function exportExcel() {
            var nama = '{{ $karyawan->nama_karyawan }}';
            var rows = [];

            // Header
            rows.push(['Kode Transaksi', 'Tanggal', 'Barang', 'Total Belanja']);

            // Data rows
            $('#tabelRiwayat tbody tr').each(function () {
                var cols = [];
                $(this).find('td').each(function (index) {
                    if (index === 2) { // Column for Barang
                        var items = [];
                        $(this).find('li').each(function () {
                            items.push($(this).text().trim());
                        });
                        cols.push(items.join(', '));
                    } else {
                        cols.push($(this).text().trim());
                    }
                });
                if (cols.length > 0) rows.push(cols);
            });

            // Total row
            rows.push(['', '', 'Total Semua Transaksi:', '{{ "Rp " . number_format($karyawan->transaksis->sum("total_belanja"), 0, ",", ".") }}']);

            // Build CSV with BOM for Excel
            var csvContent = '\uFEFF';
            csvContent += 'Riwayat Transaksi - ' + nama + '\n\n';
            rows.forEach(function (row) {
                csvContent += row.join(';') + '\n';
            });

            // Download
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'Riwayat_Transaksi_' + nama.replace(/\s+/g, '_') + '.csv';
            link.click();
        }
    </script>
@endpush