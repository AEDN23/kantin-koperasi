@extends('layouts.app')

@section('title', 'Edit Jabatan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Jabatan</h2>
        <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('jabatan.update', $jabatan) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nama_jabatan" class="form-label">Nama Jabatan <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_jabatan') is-invalid @enderror"
                        id="nama_jabatan" name="nama_jabatan"
                        value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" required>
                    @error('nama_jabatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi"
                        rows="3">{{ old('deskripsi', $jabatan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
            </form>
        </div>
    </div>
@endsection
