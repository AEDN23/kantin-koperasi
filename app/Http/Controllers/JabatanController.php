<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Transaksi;
use App\Exports\TransaksiJabatanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::withCount('karyawans')->latest()->get();
        return view('jabatan.index', compact('jabatans'));
    }

    public function create()
    {
        return view('jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan',
            'deskripsi' => 'nullable|string',
        ]);

        Jabatan::create($request->all());

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan!');
    }

    public function show(Request $request, Jabatan $jabatan)
    {
        $jabatan->load(['karyawans.departemen']);

        $karyawanIds = $jabatan->karyawans->pluck('id');

        $query = Transaksi::whereIn('karyawan_id', $karyawanIds)
            ->with(['karyawan.jabatan', 'transaksiDetails.barang']);

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $sort = $request->get('sort', 'desc');
        $transaksis = $query->orderBy('created_at', $sort)->get();

        return view('jabatan.show', compact('jabatan', 'transaksis'));
    }

    public function exportExcel(Request $request, Jabatan $jabatan)
    {
        if ($request->filled('ids')) {
            $idList = array_values(array_filter(explode(',', $request->ids)));
            $transaksis = Transaksi::whereIn('id', $idList)
                ->with(['karyawan.departemen', 'transaksiDetails.barang'])
                ->get();

            $idOrderMap = array_flip($idList);
            $transaksis = $transaksis->sortBy(fn($t) => $idOrderMap[$t->id] ?? 999999)->values();
        } else {
            $jabatan->load(['karyawans.departemen']);
            $karyawanIds = $jabatan->karyawans->pluck('id');

            $query = Transaksi::whereIn('karyawan_id', $karyawanIds)
                ->with(['karyawan.departemen', 'transaksiDetails.barang']);

            if ($request->filled('dari')) {
                $query->whereDate('created_at', '>=', $request->dari);
            }
            if ($request->filled('sampai')) {
                $query->whereDate('created_at', '<=', $request->sampai);
            }

            $transaksis = $query->orderBy('created_at', 'desc')->get();
        }

        $namaJabatan = $jabatan->nama_jabatan;
        $periodeLabel = '';
        if ($request->filled('dari') && $request->filled('sampai')) {
            $periodeLabel = 'Periode: ' . \Carbon\Carbon::parse($request->dari)->format('d/m/Y')
                . ' s/d ' . \Carbon\Carbon::parse($request->sampai)->format('d/m/Y');
        } elseif ($request->filled('dari')) {
            $periodeLabel = 'Dari: ' . \Carbon\Carbon::parse($request->dari)->format('d/m/Y');
        } elseif ($request->filled('sampai')) {
            $periodeLabel = 'Sampai: ' . \Carbon\Carbon::parse($request->sampai)->format('d/m/Y');
        } else {
            $periodeLabel = 'Semua Periode';
        }

        if ($request->filled('filter_search')) {
            $periodeLabel .= ' | Filter: "' . $request->filter_search . '"';
        }

        $dicetakPada = now()->format('d/m/Y H:i') . ' WIB';
        $safeJabatan = preg_replace('/[^A-Za-z0-9_\-]/', '_', $namaJabatan);
        $filename    = 'Transaksi_Jabatan_' . $safeJabatan . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new TransaksiJabatanExport($transaksis, $namaJabatan, $periodeLabel, $dicetakPada),
            $filename
        );
    }

    public function edit(Jabatan $jabatan)
    {
        return view('jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan,' . $jabatan->id,
            'deskripsi' => 'nullable|string',
        ]);

        $jabatan->update($request->all());

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil diupdate!');
    }

    public function destroy(Jabatan $jabatan)
    {
        if ($jabatan->karyawans()->count() > 0) {
            return redirect()->route('jabatan.index')
                ->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh karyawan!');
        }

        $jabatan->delete();

        return redirect()->route('jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus!');
    }
}
