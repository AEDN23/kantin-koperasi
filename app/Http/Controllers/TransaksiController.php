<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Karyawan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::with('departemen')
            ->join('departemens', 'karyawans.departemen_id', '=', 'departemens.id')
            ->orderBy('departemens.nama_departemen')
            ->orderBy('karyawans.nama_karyawan')
            ->select('karyawans.*')
            ->get();
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('transaksi.create', compact('karyawans', 'barangs'));
    }

    public function create()
    {
        return redirect()->route('transaksi.index');
    }

    public function riwayat()
    {
        $transaksis = Transaksi::with('karyawan', 'transaksiDetails.barang')
            ->latest()
            ->get();
        return view('transaksi.riwayat', compact('transaksis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // Generate kode transaksi: TRX-202602-001
            $lastTrx = Transaksi::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count() + 1;
            $kodeTransaksi = 'TRX-' . now()->format('Ym') . '-' . str_pad($lastTrx, 3, '0', STR_PAD_LEFT);

            // Simpan transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'karyawan_id' => $request->karyawan_id,
                'keterangan' => $request->keterangan,
            ]);

            // Simpan detail item
            foreach ($request->items as $item) {
                $barang = Barang::find($item['barang_id']);

                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $barang->harga_jual,
                    'total_harga' => $barang->harga_jual * $item['jumlah'],
                ]);

                // Kurangi stok
                $barang->decrement('stok', $item['jumlah']);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load('karyawan', 'transaksiDetails.barang');
        return view('transaksi.show', compact('transaksi'));
    }

    public function destroy(Transaksi $transaksi)
    {
        DB::transaction(function () use ($transaksi) {
            // Kembalikan stok
            foreach ($transaksi->transaksiDetails as $detail) {
                $barang = Barang::find($detail->barang_id);
                $barang->increment('stok', $detail->jumlah);
            }

            $transaksi->transaksiDetails()->delete();
            $transaksi->delete();
        });

        return redirect()->route('transaksi.riwayat')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}