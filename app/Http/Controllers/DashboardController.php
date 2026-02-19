<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);

        $totalKaryawan = Karyawan::count();
        $totalBarang = Barang::count();
        $totalTransaksi = Transaksi::whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->count();
        $totalBelanja = TransaksiDetail::whereHas('transaksi', function ($q) use ($selectedMonth, $selectedYear) {
            $q->whereMonth('created_at', $selectedMonth)
                ->whereYear('created_at', $selectedYear);
        })->sum('total_harga');

        // 1. Line Chart: Qty Barang Terjual Per Hari (1-31)
        $chartLineSales = TransaksiDetail::selectRaw('DATE(transaksis.created_at) as date, SUM(transaksi_details.jumlah) as total_qty')
            ->join('transaksis', 'transaksi_details.transaksi_id', '=', 'transaksis.id')
            ->whereMonth('transaksis.created_at', $selectedMonth)
            ->whereYear('transaksis.created_at', $selectedYear)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 2. Bar Chart 1: Top 10 Barang (Sering Dibeli - Berdasarkan Qty)
        $chartTopBarang = TransaksiDetail::select('barangs.nama_barang')
            ->selectRaw('SUM(transaksi_details.jumlah) as total_qty')
            ->join('barangs', 'transaksi_details.barang_id', '=', 'barangs.id')
            ->join('transaksis', 'transaksi_details.transaksi_id', '=', 'transaksis.id')
            ->whereMonth('transaksis.created_at', $selectedMonth)
            ->whereYear('transaksis.created_at', $selectedYear)
            ->groupBy('barangs.id', 'barangs.nama_barang')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // 3. Bar Chart 2: Top 10 Karyawan (Sering Beli - Berdasarkan Frekuensi Transaksi)
        $chartTopKaryawan = Karyawan::select('karyawans.nama_karyawan')
            ->selectRaw('COUNT(transaksis.id) as total_transaksi')
            ->join('transaksis', 'karyawans.id', '=', 'transaksis.karyawan_id')
            ->whereMonth('transaksis.created_at', $selectedMonth)
            ->whereYear('transaksis.created_at', $selectedYear)
            ->groupBy('karyawans.id', 'karyawans.nama_karyawan')
            ->orderByDesc('total_transaksi')
            ->limit(10)
            ->get();

        // 4. Pie Chart 1: Total Pengeluaran Per Departemen
        $chartDepartemen = \App\Models\Departemens::select('departemens.nama_departemen')
            ->selectRaw('SUM(transaksi_details.total_harga) as total_spending')
            ->join('karyawans', 'departemens.id', '=', 'karyawans.departemen_id')
            ->join('transaksis', 'karyawans.id', '=', 'transaksis.karyawan_id')
            ->join('transaksi_details', 'transaksis.id', '=', 'transaksi_details.transaksi_id')
            ->whereMonth('transaksis.created_at', $selectedMonth)
            ->whereYear('transaksis.created_at', $selectedYear)
            ->groupBy('departemens.id', 'departemens.nama_departemen')
            ->get();

        // 5. Pie Chart 2: Penjualan Per Kategori Barang
        $chartCategory = \App\Models\Kategori::select('kategoris.nama_kategori')
            ->selectRaw('SUM(transaksi_details.jumlah) as total_qty')
            ->join('barangs', 'kategoris.id', '=', 'barangs.kategori_id')
            ->join('transaksi_details', 'barangs.id', '=', 'transaksi_details.barang_id')
            ->join('transaksis', 'transaksi_details.transaksi_id', '=', 'transaksis.id')
            ->whereMonth('transaksis.created_at', $selectedMonth)
            ->whereYear('transaksis.created_at', $selectedYear)
            ->groupBy('kategoris.id', 'kategoris.nama_kategori')
            ->get();

        // 6. Pie Chart 3: Distribusi Qty Barang Per Departemen
        $chartDeptQty = \App\Models\Departemens::select('departemens.nama_departemen')
            ->selectRaw('SUM(transaksi_details.jumlah) as total_qty')
            ->join('karyawans', 'departemens.id', '=', 'karyawans.departemen_id')
            ->join('transaksis', 'karyawans.id', '=', 'transaksis.karyawan_id')
            ->join('transaksi_details', 'transaksis.id', '=', 'transaksi_details.transaksi_id')
            ->whereMonth('transaksis.created_at', $selectedMonth)
            ->whereYear('transaksis.created_at', $selectedYear)
            ->groupBy('departemens.id', 'departemens.nama_departemen')
            ->get();

        return view('dashboard', compact(
            'totalKaryawan',
            'totalBarang',
            'totalTransaksi',
            'totalBelanja',
            'selectedMonth',
            'selectedYear',
            'chartLineSales',
            'chartTopBarang',
            'chartTopKaryawan',
            'chartDepartemen',
            'chartCategory',
            'chartDeptQty'
        ));
    }
}