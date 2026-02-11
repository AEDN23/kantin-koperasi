<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;        

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Karyawan::count();
        $totalBarang = Barang::count();
        $totalTransaksi = Transaksi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalBelanja = TransaksiDetail::whereHas('transaksi', function ($q) {
            $q->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
        })->sum('total_harga');

        return view('dashboard', compact(
            'totalKaryawan',
            'totalBarang',
            'totalTransaksi',
            'totalBelanja'
        ));
    }
}