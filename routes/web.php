<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

/*
|--------------------------------------------------------------------------
| Web Routes - Warung Koperasi
|--------------------------------------------------------------------------
*/

// Halaman utama: jika guest -> transaksi baru (kasir), jika login -> sesuai role
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('transaksi.index');
    }
    if (auth()->user()->isAdmin()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('transaksi.riwayat');
});

// ==========================================
// 1. AUTENTIKASI (LOGIN & LOGOUT)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 2. KASIR / TRANSAKSI BARU (PUBLIK / TANPA LOGIN)
// ==========================================
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');
Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
Route::post('/transaksi/scan', [TransaksiController::class, 'scanBarcode'])->name('transaksi.scan');

// ==========================================
// 3. AKSES KARYAWAN & ADMIN (DIPROTEKSI LOGIN)
// ==========================================
Route::middleware(['auth', 'role:admin,karyawan'])->group(function () {
    // Riwayat Transaksi (Karyawan hanya miliknya, Admin semua)
    Route::get('/transaksi/riwayat', [TransaksiController::class, 'riwayat'])->name('transaksi.riwayat');
    Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');

    // Laporan (Karyawan hanya miliknya, Admin semua)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

// ==========================================
// 4. AKSES KHUSUS ADMIN (FULL ACCESS)
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Hapus Transaksi
    Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

    // Master Data
    Route::resource('departemen', DepartemenController::class)->parameters(['departemen' => 'departemen']);
    Route::match(['get', 'post'], 'jabatan/{jabatan}/export-excel', [JabatanController::class, 'exportExcel'])->name('jabatan.export-excel');
    Route::resource('jabatan', JabatanController::class);
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('kategori', KategoriController::class);

    // Barang & Stok
    Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
    Route::get('barang/export-pdf', [BarangController::class, 'exportPdf'])->name('barang.export-pdf');
    Route::get('barang/download-template', [BarangController::class, 'downloadTemplate'])->name('barang.download-template');
    Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
    Route::post('barang/{barang}/generate-qr', [BarangController::class, 'generateQrCode'])->name('barang.generate-qr');
    Route::resource('barang', BarangController::class);
    Route::resource('tambah-stok', TambahStokController::class);

    // Pelunasan Piutang
    Route::get('piutang', [PiutangController::class, 'index'])->name('piutang.index');
    Route::get('piutang/{karyawan}', [PiutangController::class, 'show'])->name('piutang.show');
    Route::post('piutang/{karyawan}/bayar', [PiutangController::class, 'bayar'])->name('piutang.bayar');
    Route::post('piutang/{karyawan}/bayar-semua', [PiutangController::class, 'bayarSemua'])->name('piutang.bayar-semua');
});