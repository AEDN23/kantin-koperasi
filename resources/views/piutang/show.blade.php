@extends('layouts.app')

@section('title', 'Detail Piutang: ' . $karyawan->nama_karyawan)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Detail Piutang</h2>
            <p class="text-muted">{{ $karyawan->nama_karyawan }} ({{ $karyawan->departemen->nama_departemen ?? '-' }})</p>
        </div>
        <a href="{{ route('piutang.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-primary text-white py-3d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Ringkasan</h5>
                        <span class="badge bg-white text-primary">{{ $piutangs->count() }} Item Unpaid</span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Total Belum Lunas:</span>
                                <span class="fw-bold text-danger fs-5">Rp {{ number_format($piutangs->sum('total_harga'), 0, ',', '.') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Total Sudah Lunas:</span>
                                <span class="fw-bold text-success text-end">Rp {{ number_format($piutangs_lunas->sum('total_harga'), 0, ',', '.') }}</span>
                            </li>
                        </ul>

                        <hr>

                        @if($piutangs->count() > 0)
                            <form action="{{ route('piutang.bayar-semua', $karyawan) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin melunasi SELURUH piutang karyawan ini?')">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm mb-2">
                                    <i class="bi bi-check-all fs-5"></i> LUNASI SEMUA
                                </button>
                            </form>
                            <p class="small text-center text-muted">Aksi ini akan menandai semua item piutang menjadi <strong>LUNAS</strong>.</p>
                        @else
                            <div class="alert alert-success text-center border-0 shadow-sm">
                                <i class="bi bi-check-circle-fill fs-3 d-block mb-2"></i>
                                Semua piutang telah lunas!
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <ul class="nav nav-pills card-header-pills" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold" id="pills-unpaid-tab" data-bs-toggle="pill" data-bs-target="#pills-unpaid" type="button" role="tab">
                                    <i class="bi bi-hourglass-split"></i> Belum Lunas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="pills-paid-tab" data-bs-toggle="pill" data-bs-target="#pills-paid" type="button" role="tab">
                                    <i class="bi bi-journal-check"></i> Riwayat Pelunasan
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="pills-tabContent">
                            <!-- Tab Belum Lunas -->
                            <div class="tab-pane fade show active" id="pills-unpaid" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Daftar Transaksi Aktif</h5>
                                    <form action="{{ route('piutang.show', $karyawan) }}" method="GET" class="d-flex align-items-center gap-2">
                                        {{-- Filter Bulan --}}
                                        <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">Semua Bulan</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                                    {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>

                                        {{-- Filter Tahun --}}
                                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">Semua Tahun</option>
                                            @for ($y = date('Y'); $y >= 2024; $y--)
                                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>

                                        {{-- Sort --}}
                                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="desc" {{ $sort == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                            <option value="asc" {{ $sort == 'asc' ? 'selected' : '' }}>Terlama</option>
                                        </select>

                                        @if($month || $year || $sort != 'desc')
                                            <a href="{{ route('piutang.show', $karyawan) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </a>
                                        @endif
                                    </form>
                                </div>

                                <form action="{{ route('piutang.bayar', $karyawan) }}" method="POST">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="40">
                                                        <input type="checkbox" id="checkAll" class="form-check-input">
                                                    </th>
                                                    <th>Waktu Hutang</th>
                                                    <th>Item & Kode</th>
                                                    <th class="text-end">Total</th>
                                                    <th class="text-center">Umur Piutang</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($piutangs as $p)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="ids[]" value="{{ $p->id }}" 
                                                            class="form-check-input item-check" 
                                                            data-price="{{ $p->total_harga }}">
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold">{{ $p->transaksi->created_at->format('d M Y') }}</div>
                                                        <small class="text-muted">{{ $p->transaksi->created_at->format('H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold text-primary">{{ $p->barang->nama_barang ?? 'Barang Terhapus' }}</div>
                                                        <small class="text-muted">{{ $p->transaksi->kode_transaksi }} | {{ $p->jumlah }} item</small>
                                                    </td>
                                                    <td class="text-end fw-bold">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                                    <td class="text-center">
                                                        @php
                                                            $days = $p->transaksi->created_at->diffInDays(now());
                                                        @endphp
                                                        @if($days == 0)
                                                            <span class="badge bg-info">Hari ini</span>
                                                        @elseif($days < 7)
                                                            <span class="badge bg-warning text-dark">{{ $days }} hari</span>
                                                        @else
                                                            <span class="badge bg-danger">{{ $days }} hari</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5 text-muted">
                                                        <i class="bi bi-emoji-smile fs-1 d-block mb-2"></i>
                                                        Tidak ada piutang aktif.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($piutangs->count() > 0)
                                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded-3 border">
                                        <div>
                                            <span class="fw-bold fs-5 text-primary" id="selectedCount">0</span> item terpilih
                                            <span class="mx-2 text-muted">|</span>
                                            Total: <span class="fw-bold fs-5 text-success">Rp <span id="selectedTotal">0</span></span>
                                        </div>
                                        <button type="submit" id="btnPaySelected" class="btn btn-primary px-4 fw-bold shadow-sm" disabled>
                                            <i class="bi bi-wallet2 me-1"></i> LUNASI TERPILIH
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>

                        <!-- Tab Sudah Lunas -->
                        <div class="tab-pane fade" id="pills-paid" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0 text-success">Riwayat Pelunasan Terakhir</h5>
                                <div class="badge bg-success py-2 px-3">
                                    Total Terbayar: Rp {{ number_format($piutangs_lunas->sum('total_harga'), 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Waktu Transaksi</th>
                                            <th>Item & Kode</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-center">Tanggal Lunas</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($piutangs_lunas as $pl)
                                            <tr>
                                                <td>
                                                    <div class="small fw-bold">{{ $pl->transaksi->created_at->format('d/m/Y') }}</div>
                                                    <small class="text-muted">{{ $pl->transaksi->kode_transaksi }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $pl->barang->nama_barang ?? 'Barang Terhapus' }}</div>
                                                    <small class="text-muted">{{ $pl->jumlah }} item</small>
                                                </td>
                                                <td class="text-end">Rp {{ number_format($pl->total_harga, 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    <div class="small fw-bold">{{ $pl->updated_at->format('d/m/Y') }}</div>
                                                    <small class="text-muted">{{ $pl->updated_at->format('H:i') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Lunas</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat pelunasan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const $checkAll = $('#checkAll');
            const $itemChecks = $('.item-check');
            const $btnPay = $('#btnPaySelected');
            const $countDisplay = $('#selectedCount');
            const $totalDisplay = $('#selectedTotal');

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

            function updateUI() {
                let checkedCount = 0;
                let totalPrice = 0;

                $itemChecks.each(function() {
                    if ($(this).is(':checked')) {
                        checkedCount++;
                        totalPrice += parseInt($(this).data('price'));
                    }
                });

                $countDisplay.text(checkedCount);
                $totalDisplay.text(formatRupiah(totalPrice));
                $btnPay.prop('disabled', checkedCount === 0);
            }

            $checkAll.on('change', function() {
                $itemChecks.prop('checked', $(this).is(':checked'));
                updateUI();
            });

            $itemChecks.on('change', function() {
                const totalChecks = $itemChecks.length;
                const totalChecked = $itemChecks.filter(':checked').length;
                $checkAll.prop('checked', totalChecks === totalChecked);
                updateUI();
            });
        });
    </script>
@endpush
