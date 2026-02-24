@extends('layouts.app')

@section('title', 'Detail Barang - ' . $barang->nama_barang)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Detail Barang</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Data Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $barang->nama_barang }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('barang.edit', $barang) }}" class="btn btn-warning shadow-sm">
                <i class="bi bi-pencil me-1"></i> Edit Barang
            </a>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Card Utama: Informasi Dasar -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-primary">Informasi Produk</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="p-4 bg-light rounded-4 d-inline-block shadow-sm">
                                <i class="bi bi-box-seam display-1 text-primary"></i>
                            </div>
                            <div class="mt-3">
                                <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">
                                    {{ $barang->kode_barang }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h3 class="fw-bold text-dark mb-1">{{ $barang->nama_barang }}</h3>
                            <p class="text-muted mb-3">{{ $barang->kategori->nama_kategori ?? 'Tanpa Kategori' }}</p>
                            
                            <div class="alert alert-light border-0 py-3 rounded-4 shadow-sm mb-0">
                                <label class="text-uppercase small fw-bold text-muted d-block mb-1">Deskripsi Produk</label>
                                <p class="mb-0 text-dark italic">
                                    {{ $barang->deskripsi ?? 'Tidak ada deskripsi untuk barang ini.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-4 bg-white shadow-sm h-100">
                                <label class="text-muted small fw-bold text-uppercase d-block mb-1">Harga Beli</label>
                                <h4 class="fw-bold text-dark mb-0">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-4 bg-white shadow-sm h-100">
                                <label class="text-muted small fw-bold text-uppercase d-block mb-1">Harga Jual</label>
                                <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Info Stok & Statistik -->
        <div class="col-lg-4">
            <div class="row g-4">
                <!-- Card Stok -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-body p-0">
                            <div class="p-4 text-center {{ $barang->stok <= $barang->stok_minimal ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                <h6 class="text-uppercase fw-bold opacity-75 mb-1">Stok Saat Ini</h6>
                                <h1 class="display-3 fw-bold mb-0">{{ $barang->stok }}</h1>
                                <p class="mb-0 small fw-bold">Unit</p>
                            </div>
                            <div class="p-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted fw-bold">Stok Minimal</span>
                                    <span class="text-dark fw-bold">{{ $barang->stok_minimal }} Unit</span>
                                </div>
                                <div class="progress rounded-pill shadow-sm" style="height: 10px;">
                                    @php
                                        $percent = ($barang->stok > 0) ? ($barang->stok / ($barang->stok + $barang->stok_minimal)) * 100 : 0;
                                        if ($percent > 100) $percent = 100;
                                    @endphp
                                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $barang->stok <= $barang->stok_minimal ? 'bg-danger' : 'bg-success' }}" 
                                         role="progressbar" style="width: {{ $percent }}%"></div>
                                </div>
                                <div class="mt-3 text-center small text-muted">
                                    @if($barang->stok <= $barang->stok_minimal)
                                        <div class="text-danger fw-bold">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Stok sudah di bawah ambang batas!
                                        </div>
                                    @else
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Stok aman tersedia</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Metadata -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-4">Metadata</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-3 bg-light p-2 me-3">
                                    <i class="bi bi-calendar-event text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Terdaftar Pada</small>
                                    <span class="fw-bold">{{ $barang->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 bg-light p-2 me-3">
                                    <i class="bi bi-clock-history text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Update Terakhir</small>
                                    <span class="fw-bold">{{ $barang->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection