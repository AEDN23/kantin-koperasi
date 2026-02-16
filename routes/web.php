<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TambahStokController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;

// Halaman utama redirect ke dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Master Data (CRUD lengkap)
Route::resource('departemen', DepartemenController::class)->parameters(['departemen' => 'departemen']);
Route::resource('karyawan', KaryawanController::class);
Route::resource('kategori', KategoriController::class);
Route::resource('barang', BarangController::class);
Route::resource('tambah-stok', TambahStokController::class);

// Transaksi
Route::get('transaksi/riwayat', [TransaksiController::class, 'riwayat'])->name('transaksi.riwayat');
Route::resource('transaksi', TransaksiController::class);

// Laporan
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');