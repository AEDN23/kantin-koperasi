<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Departemens;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::with('departemen')->latest()->get();
        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        $departemens = Departemens::all();
        return view('karyawan.create', compact('departemens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|string|max:50',
            'nama_karyawan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'departemen_id' => 'nullable|exists:departemens,id',
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
        return view('karyawan.edit', compact('karyawan', 'departemens'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nip' => 'nullable|string|max:50',
            'nama_karyawan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'departemen_id' => 'nullable|exists:departemens,id',
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