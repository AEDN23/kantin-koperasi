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
        // Ambil list karyawan yang punya piutang belum lunas
        $karyawans = Karyawan::whereHas('transaksis.transaksiDetails', function ($q) {
            $q->where('status_pembayaran', 'belum_lunas');
        })->with(['departemen'])->get()->map(function ($karyawan) {
            $karyawan->total_piutang = TransaksiDetail::whereHas('transaksi', function ($q) use ($karyawan) {
                $q->where('karyawan_id', $karyawan->id);
            })->where('status_pembayaran', 'belum_lunas')->sum('total_harga');
            return $karyawan;
        });

        return view('piutang.index', compact('karyawans'));
    }

    public function show(Request $request, Karyawan $karyawan)
    {
        $sort = $request->get('sort', 'desc');

        // Piutang yang belum lunas
        $piutangs = TransaksiDetail::with(['transaksi', 'barang'])
            ->whereHas('transaksi', function ($q) use ($karyawan) {
                $q->where('karyawan_id', $karyawan->id);
            })
            ->where('status_pembayaran', 'belum_lunas')
            ->orderBy('created_at', $sort)
            ->get();

        // Riwayat piutang yang sudah lunas
        $piutangs_lunas = TransaksiDetail::with(['transaksi', 'barang'])
            ->whereHas('transaksi', function ($q) use ($karyawan) {
                $q->where('karyawan_id', $karyawan->id);
            })
            ->where('metode_pembayaran', 'piutang')
            ->where('status_pembayaran', 'lunas')
            ->orderBy('updated_at', 'desc')
            ->limit(50) // Batasi history terakhir
            ->get();

        return view('piutang.show', compact('karyawan', 'piutangs', 'piutangs_lunas', 'sort'));
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
