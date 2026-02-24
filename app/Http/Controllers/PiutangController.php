<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    public function index()
    {
        // Ambil list karyawan yang PERNAH punya transaksi piutang
        $karyawans = Karyawan::whereHas('transaksis.transaksiDetails', function ($q) {
            $q->where('metode_pembayaran', 'piutang');
        })->with(['departemen'])->get()->map(function ($karyawan) {
            // Hitung sisa piutang (belum lunas)
            $karyawan->total_piutang = TransaksiDetail::whereHas('transaksi', function ($q) use ($karyawan) {
                $q->where('karyawan_id', $karyawan->id);
            })->where('status_pembayaran', 'belum_lunas')->sum('total_harga');

            // Hitung yang sudah dilunasi (untuk indikator)
            $karyawan->total_lunas = TransaksiDetail::whereHas('transaksi', function ($q) use ($karyawan) {
                $q->where('karyawan_id', $karyawan->id);
            })->where('metode_pembayaran', 'piutang')
                ->where('status_pembayaran', 'lunas')->sum('total_harga');

            return $karyawan;
        });

        return view('piutang.index', compact('karyawans'));
    }

    public function show(Request $request, Karyawan $karyawan)
    {
        $sort = $request->get('sort', 'desc');
        $month = $request->get('month');
        $year = $request->get('year');

        // Base query for unpaid
        $unpaidQuery = TransaksiDetail::with(['transaksi', 'barang'])
            ->whereHas('transaksi', function ($q) use ($karyawan) {
                $q->where('karyawan_id', $karyawan->id);
            })
            ->where('status_pembayaran', 'belum_lunas');

        // Base query for paid
        $paidQuery = TransaksiDetail::with(['transaksi', 'barang'])
            ->whereHas('transaksi', function ($q) use ($karyawan) {
                $q->where('karyawan_id', $karyawan->id);
            })
            ->where('metode_pembayaran', 'piutang')
            ->where('status_pembayaran', 'lunas');

        // Apply filters if month/year are provided
        if ($month) {
            $unpaidQuery->whereMonth('created_at', $month);
            $paidQuery->whereMonth('created_at', $month);
        }
        if ($year) {
            $unpaidQuery->whereYear('created_at', $year);
            $paidQuery->whereYear('created_at', $year);
        }

        $piutangs = $unpaidQuery->orderBy('created_at', $sort)->get();
        $piutangs_lunas = $paidQuery->orderBy('updated_at', 'desc')->get();

        return view('piutang.show', compact('karyawan', 'piutangs', 'piutangs_lunas', 'sort', 'month', 'year'));
    }

    public function bayar(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transaksi_details,id'
        ]);

        TransaksiDetail::whereIn('id', $request->ids)
            ->whereHas('transaksi', function ($q) use ($karyawan) {
                $q->where('karyawan_id', $karyawan->id);
            })
            ->update(['status_pembayaran' => 'lunas']);

        return redirect()->route('piutang.index')
            ->with('success', 'Piutang berhasil dilunasi!');
    }

    public function bayarSemua(Karyawan $karyawan)
    {
        TransaksiDetail::whereHas('transaksi', function ($q) use ($karyawan) {
            $q->where('karyawan_id', $karyawan->id);
        })
            ->where('status_pembayaran', 'belum_lunas')
            ->update(['status_pembayaran' => 'lunas']);

        return redirect()->route('piutang.index')
            ->with('success', 'Semua piutang karyawan berhasil dilunasi!');
    }
}
