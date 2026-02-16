@extends('layouts.app')

@section('title', 'Detail Departemen')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Departemen</h2>
        <a href="{{ route('departemen.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th width="200">Nama Departemen</th>
                    <td>{{ $departemen->nama_departemen }}</td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td>{{ $departemen->deskripsi ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Jumlah Karyawan</th>
                    <td>{{ $departemen->karyawans->count() }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection