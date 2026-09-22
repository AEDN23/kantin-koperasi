@extends('layouts.app')

@section('title', 'Tambah Line')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah Line</h2>
        <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('jabatan.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama_jabatan" class="form-label">Nama Line <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_jabatan') is-invalid @enderror"
                        id="nama_jabatan" name="nama_jabatan" value="{{ old('nama_jabatan') }}"
                        placeholder="Contoh: Ketua, Sekretaris, Bendahara, Staff, Manager" required>
                    @error('nama_jabatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi"
                        rows="3" placeholder="Deskripsi tugas atau wewenang jabatan (opsional)">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
@endsection
