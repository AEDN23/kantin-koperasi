<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Departemens;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::with(['departemen', 'jabatan'])->latest()->get();

        // Agregasi total transaksi belanja dan frekuensi transaksi per karyawan
        $transaksiSummary = DB::table('transaksis')
            ->leftJoin('transaksi_details', 'transaksis.id', '=', 'transaksi_details.transaksi_id')
            ->select('transaksis.karyawan_id')
            ->selectRaw('COUNT(DISTINCT transaksis.id) as total_count')
            ->selectRaw('COALESCE(SUM(transaksi_details.total_harga), 0) as total_belanja')
            ->groupBy('transaksis.karyawan_id')
            ->get()
            ->keyBy('karyawan_id');

        // Agregasi total piutang yang belum lunas per karyawan
        $piutangSummary = DB::table('transaksis')
            ->join('transaksi_details', 'transaksis.id', '=', 'transaksi_details.transaksi_id')
            ->where('transaksi_details.metode_pembayaran', 'piutang')
            ->where('transaksi_details.status_pembayaran', 'belum_lunas')
            ->select('transaksis.karyawan_id')
            ->selectRaw('COALESCE(SUM(transaksi_details.total_harga), 0) as total_piutang')
            ->groupBy('transaksis.karyawan_id')
            ->get()
            ->keyBy('karyawan_id');

        $karyawans->each(function ($karyawan) use ($transaksiSummary, $piutangSummary) {
            $trx = $transaksiSummary->get($karyawan->id);
            $piutang = $piutangSummary->get($karyawan->id);

            $karyawan->transaksi_count = $trx ? $trx->total_count : 0;
            $karyawan->total_transaksi = $trx ? (int) $trx->total_belanja : 0;
            $karyawan->total_piutang   = $piutang ? (int) $piutang->total_piutang : 0;
        });

        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        $departemens = Departemens::all();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        return view('karyawan.create', compact('departemens', 'jabatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|string|max:50',
            'nama_karyawan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'departemen_id' => 'nullable|exists:departemens,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Karyawan::create($request->all());

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function show(Request $request, Karyawan $karyawan)
    {
        $sort = $request->get('sort', 'desc');
        $karyawan->load([
            'departemen',
            'jabatan',
            'transaksis' => function ($q) use ($request, $sort) {
                if ($request->filled('dari')) {
                    $q->whereDate('created_at', '>=', $request->dari);
                }
                if ($request->filled('sampai')) {
                    $q->whereDate('created_at', '<=', $request->sampai);
                }
                $q->orderBy('created_at', $sort);
            },
            'transaksis.transaksiDetails.barang'
        ]);
        return view('karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        $departemens = Departemens::all();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        return view('karyawan.edit', compact('karyawan', 'departemens', 'jabatans'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nip' => 'nullable|string|max:50',
            'nama_karyawan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'departemen_id' => 'nullable|exists:departemens,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil diupdate!');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil dihapus!');
    }
}