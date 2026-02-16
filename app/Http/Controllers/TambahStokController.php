<?php

namespace App\Http\Controllers;

use App\Models\TambahStok;
use App\Models\Barang;
use Illuminate\Http\Request;

class TambahStokController extends Controller
{
    public function index(Request $request)
    {
        $query = TambahStok::with('barang');

        // Filter tanggal
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        // Sort
        $sort = $request->get('sort', 'desc');
        $query->orderBy('tanggal', $sort);

        $tambahStoks = $query->get();
        return view('tambah-stok.index', compact('tambahStoks'));
    }

    public function create()
    {
        $barangs = Barang::all();
        return view('tambah-stok.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        TambahStok::create($request->all());

        // Update stok di tabel barang
        $barang = Barang::find($request->barang_id);
        $barang->increment('stok', $request->jumlah);

        return redirect()->route('tambah-stok.index')
            ->with('success', 'Stok berhasil ditambahkan!');
    }

    public function edit(TambahStok $tambahStok)
    {
        $barangs = Barang::all();
        return view('tambah-stok.edit', compact('tambahStok', 'barangs'));
    }

    public function update(Request $request, TambahStok $tambahStok)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        // Kembalikan stok lama, lalu tambah stok baru
        $barangLama = Barang::find($tambahStok->barang_id);
        $barangLama->decrement('stok', $tambahStok->jumlah);

        $tambahStok->update($request->all());

        $barangBaru = Barang::find($request->barang_id);
        $barangBaru->increment('stok', $request->jumlah);

        return redirect()->route('tambah-stok.index')
            ->with('success', 'Stok berhasil diupdate!');
    }

    public function destroy(TambahStok $tambahStok)
    {
        // Kurangi stok saat data dihapus
        $barang = Barang::find($tambahStok->barang_id);
        $barang->decrement('stok', $tambahStok->jumlah);

        $tambahStok->delete();

        return redirect()->route('tambah-stok.index')
            ->with('success', 'Stok berhasil dihapus!');
    }
}