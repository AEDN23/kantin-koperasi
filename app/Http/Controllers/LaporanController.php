<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // Ambil semua karyawan yang punya transaksi di bulan/tahun tersebut
        $laporans = Karyawan::with(['transaksis' => function ($query) use ($bulan, $tahun) {
            $query->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun)
                  ->with('transaksiDetails');
        }])
        ->whereHas('transaksis', function ($query) use ($bulan, $tahun) {
            $query->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun);
        })
        ->get()
        ->map(function ($karyawan) {
            $karyawan->total_belanja = $karyawan->transaksis
                ->flatMap->transaksiDetails
                ->sum('total_harga');
            $karyawan->jumlah_transaksi = $karyawan->transaksis->count();
            return $karyawan;
        });

        $grandTotal = $laporans->sum('total_belanja');

        return view('laporan.index', compact('laporans', 'bulan', 'tahun', 'grandTotal'));
    }
}