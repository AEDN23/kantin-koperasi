<?php

namespace App\Http\Controllers;

use App\Models\Departemens;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemens = Departemens::latest()->get();
        return view('departemen.index', compact('departemens'));
    }

    public function create()
    {
        return view('departemen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Departemens::create($request->all());

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan!');
    }

    public function show(Request $request, Departemens $departemen)
    {
        $query = Transaksi::whereIn('karyawan_id', $departemen->karyawans->pluck('id'))
            ->with(['karyawan', 'transaksiDetails']);

        // Filter tanggal
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $transaksis = $query->latest()->get();

        return view('departemen.show', compact('departemen', 'transaksis'));
    }

    public function edit(Departemens $departemen)
    {
        return view('departemen.edit', compact('departemen'));
    }

    public function update(Request $request, Departemens $departemen)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $departemen->update($request->all());

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil diupdate!');
    }

    public function destroy(Departemens $departemen)
    {
        $departemen->delete();

        return redirect()->route('departemen.index')
            ->with('success', 'Departemen berhasil dihapus!');
    }
}