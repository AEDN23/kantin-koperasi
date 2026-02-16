@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Kategori</h2>
        <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th width="200">Nama Kategori</th>
                    <td>{{ $kategori->nama_kategori }}</td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td>{{ $kategori->deskripsi ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Jumlah Barang</th>
                    <td>{{ $kategori->barangs->count() }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection