<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Warung Koperasi')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    @stack('styles')
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .7);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            white-space: nowrap;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, .15);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .main-content {
            background: #f4f6f9;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        /* Sidebar Toggle Styles */
        #toggleSidebar {
            cursor: pointer;
            z-index: 1001;
        }

        @media (min-width: 768px) {
            body.sidebar-toggled .sidebar {
                margin-left: -25%;
                opacity: 0;
                visibility: hidden;
                position: absolute;
            }

            body.sidebar-toggled .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
        }

        @media (max-width: 767.98px) {
            .sidebar {
                margin-left: -100%;
                position: fixed;
                width: 250px;
                z-index: 1030;
            }

            body.sidebar-toggled .sidebar {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar p-0">
                <div class="text-center py-4">
                    <h5 class="text-white fw-bold">🏪 Warung Koperasi</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <small class="text-white-50 px-3">MASTER DATA</small>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departemen.*') ? 'active' : '' }}"
                            href="{{ route('departemen.index') }}">
                            <i class="bi bi-building"></i> Departemen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('karyawan.*') ? 'active' : '' }}"
                            href="{{ route('karyawan.index') }}">
                            <i class="bi bi-people"></i> Karyawan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}"
                            href="{{ route('kategori.index') }}">
                            <i class="bi bi-tags"></i> Kategori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}"
                            href="{{ route('barang.index') }}">
                            <i class="bi bi-box"></i> Barang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tambah-stok.*') ? 'active' : '' }}"
                            href="{{ route('tambah-stok.index') }}">
                            <i class="bi bi-plus-circle"></i> Tambah Stok
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <small class="text-white-50 px-3">TRANSAKSI</small>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transaksi.index') || request()->routeIs('transaksi.create') ? 'active' : '' }}"
                            href="{{ route('transaksi.index') }}">
                            <i class="bi bi-cart-plus"></i> Transaksi Baru
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transaksi.riwayat') || request()->routeIs('transaksi.show') ? 'active' : '' }}"
                            href="{{ route('transaksi.riwayat') }}">
                            <i class="bi bi-clock-history"></i> Riwayat Transaksi
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <small class="text-white-50 px-3">LAPORAN</small>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}"
                            href="{{ route('laporan.index') }}">
                            <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content p-4">
                <div class="d-flex align-items-center mb-4">
                    <button id="toggleSidebar" class="btn btn-light shadow-sm me-3 border">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="mb-0 fw-bold text-dark">@yield('title', 'Warung Koperasi')</h4>
                </div>

                <!-- Flash Message -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery & Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Global live search untuk tabel
        $(document).ready(function () {
            // Sidebar Toggle Logic
            if (localStorage.getItem('sidebar-state') === 'toggled') {
                $('body').addClass('sidebar-toggled');
            }

            $('#toggleSidebar').on('click', function () {
                $('body').toggleClass('sidebar-toggled');
                if ($('body').hasClass('sidebar-toggled')) {
                    localStorage.setItem('sidebar-state', 'toggled');
                } else {
                    localStorage.setItem('sidebar-state', 'normal');
                }
            });

            $('#searchInput').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('.searchable-table tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>