<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TambahStokController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PiutangController;

// Halaman utama redirect ke dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Master Data (CRUD lengkap)
Route::resource('departemen', DepartemenController::class)->parameters(['departemen' => 'departemen']);
Route::match(['get', 'post'], 'jabatan/{jabatan}/export-excel', [JabatanController::class, 'exportExcel'])->name('jabatan.export-excel');
Route::resource('jabatan', JabatanController::class);
Route::resource('karyawan', KaryawanController::class);
Route::resource('kategori', KategoriController::class);
Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
Route::get('barang/export-pdf', [BarangController::class, 'exportPdf'])->name('barang.export-pdf');
Route::get('barang/download-template', [BarangController::class, 'downloadTemplate'])->name('barang.download-template');
Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
Route::post('barang/{barang}/generate-qr', [BarangController::class, 'generateQrCode'])->name('barang.generate-qr');
Route::resource('barang', BarangController::class);
Route::resource('tambah-stok', TambahStokController::class);

// Transaksi
Route::get('transaksi/riwayat', [TransaksiController::class, 'riwayat'])->name('transaksi.riwayat');
Route::post('transaksi/scan', [TransaksiController::class, 'scanBarcode'])->name('transaksi.scan');
Route::resource('transaksi', TransaksiController::class);

// Pelunasan Piutang
Route::get('piutang', [PiutangController::class, 'index'])->name('piutang.index');
Route::get('piutang/{karyawan}', [PiutangController::class, 'show'])->name('piutang.show');
Route::post('piutang/{karyawan}/bayar', [PiutangController::class, 'bayar'])->name('piutang.bayar');
Route::post('piutang/{karyawan}/bayar-semua', [PiutangController::class, 'bayarSemua'])->name('piutang.bayar-semua');

// Laporan
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');