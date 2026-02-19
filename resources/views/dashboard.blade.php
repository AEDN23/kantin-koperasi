@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-primary">Koperasi Analytics</h2>
            <p class="text-muted mb-0">Visualisasi data transaksi koperasi</p>
        </div>

        <!-- Filter Form -->
        <div class="bg-white p-2 rounded-3 shadow-sm border">
            <form action="{{ route('dashboard') }}" method="GET" class="row g-2 align-items-center m-0">
                @php
                    $months = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ];
                    $currentYear = date('Y');
                @endphp
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar3 text-primary"></i></span>
                        <select name="month" class="form-select border-0 bg-light" style="width: 130px;">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i
                                class="bi bi-calendar-event text-primary"></i></span>
                        <select name="year" class="form-select border-0 bg-light" style="width: 90px;">
                            @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                        <i class="bi bi-arrow-repeat"></i> Update
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm px-3 border shadow-sm ms-1">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="card text-white h-100 border-0"
                style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);">
                <div class="card-body">
                    <h6 class="opacity-75 mb-1">Total Karyawan</h6>
                    <h2 class="fw-bold mb-0">{{ $totalKaryawan }}</h2>
                    <div class="mt-2 small"><i class="bi bi-people"></i> Terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white h-100 border-0"
                style="background: linear-gradient(135deg, #2d5a87 0%, #3e7bb5 100%);">
                <div class="card-body">
                    <h6 class="opacity-75 mb-1">Total Barang</h6>
                    <h2 class="fw-bold mb-0">{{ $totalBarang }}</h2>
                    <div class="mt-2 small"><i class="bi bi-box-seam"></i> Item Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white h-100 border-0"
                style="background: linear-gradient(135deg, #3e7bb5 0%, #4a90e2 100%);">
                <div class="card-body">
                    <h6 class="opacity-75 mb-1">Nota Transaksi</h6>
                    <h2 class="fw-bold mb-0">{{ $totalTransaksi }}</h2>
                    <div class="mt-2 small"><i class="bi bi-cart-check"></i> Periode Ini</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white h-100 border-0"
                style="background: linear-gradient(135deg, #4a90e2 0%, #63b3ed 100%);">
                <div class="card-body">
                    <h6 class="opacity-75 mb-1">Total Penjualan</h6>
                    <h2 class="fw-bold mb-0">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</h2>
                    <div class="mt-2 small"><i class="bi bi-cash-stack"></i> Periode Ini</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Chart Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Tren Penjualan Harian</h5>
                    <small class="text-muted">(Qty Barang Terjual)</small>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="lineChartDaily" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Top 10 Barang -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-award text-warning me-2"></i>Top 10 Barang Terlaris</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="barChartBarang" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
        <!-- Top 10 Karyawan -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-star-fill text-info me-2"></i>Top 10 Karyawan (Sering Beli)
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="barChartKaryawan" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Pie Chart 1: Departemen (Spending) -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                    <h5 class="fw-bold mb-0 small text-uppercase text-muted">Pengeluaran / Departemen</h5>
                </div>
                <div class="card-body px-4 pb-4 text-center">
                    <canvas id="pieChartDept" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <!-- Pie Chart 2: Kategori (Qty) -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                    <h5 class="fw-bold mb-0 small text-uppercase text-muted">Penjualan / Kategori</h5>
                </div>
                <div class="card-body px-4 pb-4 text-center">
                    <canvas id="pieChartCategory" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <!-- Pie Chart 3: KARYAWAN (Qty) -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                    <h5 class="fw-bold mb-0 small text-uppercase text-muted">Qty Serapan / Departemen</h5>
                </div>
                <div class="card-body px-4 pb-4 text-center">
                    <canvas id="pieChartDeptQty" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const primaryColor = '#1e3a5f';
            const secondaryColor = '#4a90e2';
            const accentColors = ['#1e3a5f', '#2d5a87', '#3e7bb5', '#4facfe', '#764ba2', '#667eea', '#f093fb', '#f5576c', '#43e97b', '#38f9d7'];

            // 1. Line Chart: Daily Sales
            const dailyData = @json($chartLineSales);
            new Chart(document.getElementById('lineChartDaily'), {
                type: 'line',
                data: {
                    labels: dailyData.map(d => new Date(d.date).getDate()),
                    datasets: [{
                        label: 'Qty Jual',
                        data: dailyData.map(d => d.total_qty),
                        borderColor: primaryColor,
                        backgroundColor: 'rgba(30, 58, 95, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { title: { display: true, text: 'Tanggal' }, grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                }
            });

            // 2. Bar Chart: Top 10 Barang
            const barangData = @json($chartTopBarang);
            new Chart(document.getElementById('barChartBarang'), {
                type: 'bar',
                data: {
                    labels: barangData.map(b => b.nama_barang),
                    datasets: [{
                        label: 'Total Qty',
                        data: barangData.map(b => b.total_qty),
                        backgroundColor: secondaryColor,
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { display: false } },
                        y: { grid: { display: false } }
                    }
                }
            });

            // 3. Bar Chart: Top 10 Karyawan
            const karyawanData = @json($chartTopKaryawan);
            new Chart(document.getElementById('barChartKaryawan'), {
                type: 'bar',
                data: {
                    labels: karyawanData.map(k => k.nama_karyawan),
                    datasets: [{
                        label: ' Kali Belanja',
                        data: karyawanData.map(k => k.total_transaksi),
                        backgroundColor: '#764ba2',
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { display: false } },
                        y: { grid: { display: false } }
                    }
                }
            });

            // 4. Pie Chart 1: Departemen (Spending)
            const deptData = @json($chartDepartemen);
            new Chart(document.getElementById('pieChartDept'), {
                type: 'pie',
                data: {
                    labels: deptData.map(d => d.nama_departemen),
                    datasets: [{
                        data: deptData.map(d => d.total_spending),
                        backgroundColor: accentColors,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10 } } },
                        tooltip: {
                            callbacks: {
                                label: (context) => ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw)
                            }
                        }
                    }
                }
            });

            // 5. Pie Chart 2: Kategori
            const catData = @json($chartCategory);
            new Chart(document.getElementById('pieChartCategory'), {
                type: 'pie',
                data: {
                    labels: catData.map(c => c.nama_kategori),
                    datasets: [{
                        data: catData.map(c => c.total_qty),
                        backgroundColor: accentColors,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10 } } },
                        tooltip: {
                            callbacks: {
                                label: (context) => ' Qty: ' + context.raw
                            }
                        }
                    }
                }
            });

            // 6. Pie Chart 3: Departemen (Qty)
            const deptQtyData = @json($chartDeptQty);
            new Chart(document.getElementById('pieChartDeptQty'), {
                type: 'pie',
                data: {
                    labels: deptQtyData.map(d => d.nama_departemen),
                    datasets: [{
                        data: deptQtyData.map(d => d.total_qty),
                        backgroundColor: accentColors,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10 } } },
                        tooltip: {
                            callbacks: {
                                label: (context) => ' Qty: ' + context.raw
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush