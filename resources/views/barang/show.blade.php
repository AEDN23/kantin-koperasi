@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Barang</h2>
        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Nama Barang</th>
                    <td>{{ $barang->nama_barang }}</td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Harga Jual</th>
                    <td>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Stok</th>
                    <td><span class="badge {{ $barang->stok > 0 ? 'bg-success' : 'bg-danger' }}">{{ $barang->stok }}</span>
                    </td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td>{{ $barang->deskripsi ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection