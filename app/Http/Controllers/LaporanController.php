<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Karyawan;
use App\Models\Departemens;
use App\Models\Jabatan;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Build the laporan query based on date range and filters.
     */
    private function buildLaporanData(Request $request)
    {
        $dari = $request->dari ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? Carbon::now()->format('Y-m-d');

        $jabatan_id = $request->jabatan_id;
        $lines = Jabatan::orderBy('nama_jabatan')->get();
        $departemen_id = $request->departemen_id;
        $departemens = Departemens::orderBy('nama_departemen')->get();

        // Ambil semua karyawan yang punya transaksi di rentang tanggal tersebut
        $query = Karyawan::with([
            'jabatan',
            'departemen',
            'transaksis' => function ($query) use ($dari, $sampai) {
                $query->whereDate('created_at', '>=', $dari)
                    ->whereDate('created_at', '<=', $sampai)
                    ->with('transaksiDetails.barang');
            }
        ])
            ->whereHas('transaksis', function ($query) use ($dari, $sampai) {
                $query->whereDate('created_at', '>=', $dari)
                    ->whereDate('created_at', '<=', $sampai);
            });

        $user = auth()->user();
        $isKaryawan = $user && $user->isKaryawan();

        if ($isKaryawan) {
            $query->where('id', $user->karyawan_id);
        } elseif ($request->filled('jabatan_id')) {
            $query->where('jabatan_id', $request->jabatan_id);
        } elseif ($request->filled('departemen_id')) {
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

        return compact('laporans', 'dari', 'sampai', 'grandTotal', 'lines', 'jabatan_id', 'departemens', 'departemen_id', 'isKaryawan');
    }

    public function index(Request $request)
    {
        $data = $this->buildLaporanData($request);

        return view('laporan.index', $data);
    }

    public function export(Request $request)
    {
        $data = $this->buildLaporanData($request);

        $periodeLabel = 'Periode: ' . Carbon::parse($data['dari'])->format('d/m/Y')
            . ' s/d ' . Carbon::parse($data['sampai'])->format('d/m/Y');

        if (!empty($data['jabatan_id'])) {
            $line = $data['lines']->firstWhere('id', $data['jabatan_id']);
            if ($line) {
                $periodeLabel .= ' | Line: ' . $line->nama_jabatan;
            }
        } elseif (!empty($data['departemen_id'])) {
            $dept = $data['departemens']->firstWhere('id', $data['departemen_id']);
            if ($dept) {
                $periodeLabel .= ' | Departemen: ' . $dept->nama_departemen;
            }
        }

        $dicetakPada = now()->format('d/m/Y H:i') . ' WIB';
        $filename = 'Laporan_Bulanan_' . Carbon::parse($data['dari'])->format('Ymd') . '_' . Carbon::parse($data['sampai'])->format('Ymd') . '.xlsx';

        return Excel::download(
            new LaporanExport($data['laporans'], $data['grandTotal'], $periodeLabel, $dicetakPada),
            $filename
        );
    }
}