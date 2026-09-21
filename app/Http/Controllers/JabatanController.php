<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Transaksi;
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

        $filename = 'Transaksi_Jabatan_' . str_replace(' ', '_', $namaJabatan) . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function () use ($transaksis, $namaJabatan, $jabatan, $periodeLabel) {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel agar karakter Indonesia terbaca
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // === Judul laporan ===
            fputcsv($handle, ['LAPORAN RIWAYAT TRANSAKSI BERDASARKAN JABATAN'], ';');
            fputcsv($handle, ['Jabatan', $namaJabatan], ';');
            fputcsv($handle, [$periodeLabel], ';');
            fputcsv($handle, ['Dicetak pada', now()->format('d/m/Y H:i') . ' WIB'], ';');
            fputcsv($handle, [], ';');

            // === Header tabel ===
            fputcsv($handle, [
                'No',
                'Tanggal & Waktu',
                'Nama Karyawan',
                'Barang yang Dibeli',
                'Qty',
                'Harga Satuan',
                'Total Item',
                'Total Transaksi',
                'Metode Pembayaran',
            ], ';');

            $no       = 1;
            $grandTotal = 0;

            // Helper: format mata uang
            $rp = fn($val) => 'Rp. ' . number_format((int)$val, 0, ',', '.');

            // Helper: label metode pembayaran per-detail
            $metodeLabel = function ($detail) {
                $metode = strtolower($detail->metode_pembayaran ?? '');
                if ($metode === 'piutang') {
                    $status = strtolower($detail->status_pembayaran ?? '');
                    return 'Piutang' . ($status === 'lunas' ? ' (Lunas)' : ' (Belum Lunas)');
                }
                return ucfirst($detail->metode_pembayaran ?? '-');
            };

            foreach ($transaksis as $trx) {
                $details      = $trx->transaksiDetails;
                $jumlahDetail = $details->count();

                if ($jumlahDetail === 0) {
                    // Transaksi tanpa detail
                    fputcsv($handle, [
                        $no++,
                        $trx->created_at->format('d/m/Y H:i'),
                        $trx->karyawan->nama_karyawan ?? '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        $rp($trx->total_belanja),
                        $trx->metode_pembayaran,
                    ], ';');
                } else {
                    foreach ($details as $i => $detail) {
                        $row = [];

                        if ($i === 0) {
                            // Baris pertama: isi nomor, tanggal, nama, total transaksi
                            $row[] = $no++;
                            $row[] = $trx->created_at->format('d/m/Y H:i');
                            $row[] = $trx->karyawan->nama_karyawan ?? '-';
                        } else {
                            // Baris lanjutan: kosongkan kolom identitas transaksi
                            $row[] = '';
                            $row[] = '';
                            $row[] = '';
                        }

                        $row[] = $detail->barang->nama_barang ?? 'Barang Terhapus';
                        $row[] = $detail->jumlah;
                        $row[] = $rp($detail->harga_satuan);
                        $row[] = $rp($detail->total_harga);

                        if ($i === 0) {
                            $row[] = $rp($trx->total_belanja);
                            $row[] = $metodeLabel($detail);
                        } else {
                            $row[] = '';
                            $row[] = $metodeLabel($detail);
                        }

                        fputcsv($handle, $row, ';');
                    }
                }

                $grandTotal += $trx->total_belanja;
            }

            // === Baris total ===
            fputcsv($handle, [], ';');
            fputcsv($handle, [
                '', '', '', '', '', '', 'TOTAL KESELURUHAN',
                $rp($grandTotal), '',
            ], ';');

            fputcsv($handle, [], ';');
            fputcsv($handle, ['Total Transaksi', $transaksis->count() . ' transaksi'], ';');

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
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
