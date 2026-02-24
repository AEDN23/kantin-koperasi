<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Karyawan;
use App\Models\Departemens;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $departemen_id = $request->departemen_id;
        $departemens = Departemens::orderBy('nama_departemen')->get();

        // Ambil semua karyawan yang punya transaksi di bulan/tahun tersebut
        $query = Karyawan::with([
            'departemen',
            'transaksis' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->with('transaksiDetails.barang');
            }
        ])
            ->whereHas('transaksis', function ($query) use ($bulan, $tahun) {
                $query->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun);
            });

        if ($request->filled('departemen_id')) {
            $query->where('departemen_id', $request->departemen_id);
        }

        $laporans = $query->get()
            ->map(function ($karyawan) {
                $details = $karyawan->transaksis->flatMap->transaksiDetails;

                $karyawan->total_belanja = $details->sum('total_harga');
                $karyawan->jumlah_transaksi = $karyawan->transaksis->count();

                // Hitung Piutang (khusus di periode ini yang BELUM LUNAS)
                $karyawan->total_piutang = $details->where('metode_pembayaran', 'piutang')
                    ->where('status_pembayaran', 'belum_lunas')
                    ->sum('total_harga');

                // Hitung Estimasi Profit
                $karyawan->total_profit = $details->sum(function ($d) {
                    return ($d->harga_satuan - ($d->barang->harga_beli ?? 0)) * $d->jumlah;
                });

                return $karyawan;
            });

        $grandTotal = $laporans->sum('total_belanja');

        return view('laporan.index', compact('laporans', 'bulan', 'tahun', 'grandTotal', 'departemens', 'departemen_id'));
    }
}