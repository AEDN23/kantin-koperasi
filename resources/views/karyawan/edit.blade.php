@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Karyawan</h2>
        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('karyawan.update', $karyawan) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip"
                            value="{{ old('nip', $karyawan->nip) }}">
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_karyawan" class="form-label">Nama Karyawan <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_karyawan') is-invalid @enderror"
                            id="nama_karyawan" name="nama_karyawan"
                            value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}" required>
                        @error('nama_karyawan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                        rows="2">{{ old('alamat', $karyawan->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="departemen_id" class="form-label">Departemen</label>
                        <select class="form-select @error('departemen_id') is-invalid @enderror" id="departemen_id"
                            name="departemen_id">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemens as $departemen)
                                <option value="{{ $departemen->id }}" {{ old('departemen_id', $karyawan->departemen_id) == $departemen->id ? 'selected' : '' }}>
                                    {{ $departemen->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                        @error('departemen_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jabatan_id" class="form-label">Line</label>
                        <select class="form-select @error('jabatan_id') is-invalid @enderror" id="jabatan_id"
                            name="jabatan_id">
                            <option value="">-- Pilih Line --</option>
                            @foreach($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}" {{ old('jabatan_id', $karyawan->jabatan_id) == $jabatan->id ? 'selected' : '' }}>
                                    {{ $jabatan->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('jabatan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="no_hp" class="form-label">No HP</label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp"
                            value="{{ old('no_hp', $karyawan->no_hp) }}">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $karyawan->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card border border-primary border-opacity-25 bg-light mb-4">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-shield-lock me-1"></i> Pengaturan Akun & Hak Akses Login
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label fw-semibold">Role / Hak Akses</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                    <option value="karyawan" {{ old('role', $karyawan->user->role ?? 'karyawan') == 'karyawan' ? 'selected' : '' }}>
                                        Karyawan (Akses riwayat & laporan pribadi)
                                    </option>
                                    <option value="admin" {{ old('role', $karyawan->user->role ?? '') == 'admin' ? 'selected' : '' }}>
                                        Admin (Akses penuh seluruh sistem)
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    Pilih tingkat hak akses login untuk akun ini.
                                </small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">Ubah Password Akun</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    Password bawaan default adalah <strong>NIP</strong>. Isi kolom ini hanya jika ingin mengganti password baru.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('togglePassword');
        const passInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', function() {
                const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passInput.setAttribute('type', type);
                toggleIcon.classList.toggle('bi-eye');
                toggleIcon.classList.toggle('bi-eye-slash');
            });
        }
    });
</script>
@endpush