<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Exports\BarangTemplateExport;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new BarangImport;
        Excel::import($import, $request->file('file'));

        return redirect()->back()->with('import_results', [
            'success' => $import->successCount,
            'duplicate' => $import->duplicateCount
        ]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new BarangTemplateExport, 'template_import_barang.xlsx');
    }

    public function index()
    {
        $barangs = Barang::with('kategori')->latest()->get();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('barang.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga_beli' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'deskripsi' => 'nullable|string',
        ]);

        $data = $request->all();

        // Generate Kode Barang Otomatis
        $count = Barang::count() + 1;
        $kodeBarang = 'BRG-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        while (Barang::where('kode_barang', $kodeBarang)->exists()) {
            $count++;
            $kodeBarang = 'BRG-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $data['kode_barang'] = $kodeBarang;

        Barang::create($data);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan!');
    }

    public function show(Barang $barang)
    {
        $barang->load([
            'kategori',
            'transaksiDetails.transaksi',
            'tambahStoks' => function ($query) {
                $query->latest();
            }
        ]);

        // Sort transaksiDetails manually by created_at since it's a nested relationship
        $salesHistory = $barang->transaksiDetails->sortByDesc(function ($detail) {
            return $detail->transaksi->created_at ?? $detail->created_at;
        });

        return view('barang.show', compact('barang', 'salesHistory'));
    }

    public function edit(Barang $barang)
    {
        $kategoris = Kategori::all();
        return view('barang.edit', compact('barang', 'kategoris'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga_beli' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'deskripsi' => 'nullable|string',
        ]);

        $barang->update($request->all());

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diupdate!');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus!');
    }
}