@extends('layouts.app')

@section('title', 'Edit Departemen')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Departemen</h2>
        <a href="{{ route('departemen.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('departemen.update', $departemen) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nama_departemen" class="form-label">Nama Departemen <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_departemen') is-invalid @enderror"
                        id="nama_departemen" name="nama_departemen"
                        value="{{ old('nama_departemen', $departemen->nama_departemen) }}" required>
                    @error('nama_departemen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi"
                        rows="3">{{ old('deskripsi', $departemen->deskripsi) }}</textarea>
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