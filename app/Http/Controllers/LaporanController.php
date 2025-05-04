<?php

namespace App\Http\Controllers;

use App\Models\MutasiStok;
use App\Models\Penjualan;
use App\Models\Stok;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function laporanPenjualan(Request $request)
    {
        $query = Penjualan::withCount('detailPenjualan');

        if ($request->has('search') && $request->search) {
            $query->whereDate('tanggal_penjualan', $request->search);
        }

        $penjualans = $query->orderBy('tanggal_penjualan', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => $penjualans->map(function ($penjualan) {
                    return [
                        'id' => $penjualan->id,
                        'tanggal_penjualan' => $penjualan->tanggal_penjualan,
                        'jumlah_transaksi' => $penjualan->detail_penjualan_count,
                        'total_harga' => number_format($penjualan->total_harga, 0, ',', '.'),
                    ];
                }),
                'pagination' => [
                    'total' => $penjualans->total(),
                    'current_page' => $penjualans->currentPage(),
                    'last_page' => $penjualans->lastPage(),
                    'prev_page_url' => $penjualans->previousPageUrl(),
                    'next_page_url' => $penjualans->nextPageUrl(),
                ]
            ]);
        }

        return view('laporan.penjualan', compact('penjualans'));
    }

    public function detailPenjualan($id)
    {
        $penjualan = Penjualan::with('detailPenjualan.obat', 'detailPenjualan.konversiSatuan')->findOrFail($id);

        return response()->json([
            'data' => $penjualan->detailPenjualan->map(function ($detail) {
                return [
                    'nama_obat' => $detail->obat->nama_obat ?? 'Tidak Diketahui',
                    'satuan' => $detail->konversiSatuan->nama_satuan ?? 'Tidak Diketahui',
                    'jumlah' => $detail->jumlah,
                    'harga_satuan' => number_format($detail->harga_satuan, 0, ',', '.'),
                    'subtotal' => number_format($detail->subtotal, 0, ',', '.'),
                ];
            })
        ]);
    }


    public function laporanStok(Request $request)
    {
        $query = MutasiStok::with('stok.drug');

        if ($request->has('search') && $request->search) {
            $query->whereHas('stok.drug', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('jenis_mutasi') && $request->jenis_mutasi) {
            $query->where('mutasi_stok.jenis_mutasi', $request->jenis_mutasi);
        }

        $mutasi_stok = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => $mutasi_stok->map(function ($item) {
                    $faktor_konversi = optional($item->stok->konversiSatuan)->jumlah_satuan_terkecil ?? 1;
                    $jumlah_asli = $item->jumlah;
                    $jumlah_konversi = $jumlah_asli / $faktor_konversi;

                    // Cek apakah hasil pembagian bilangan bulat atau tidak
                    $is_bulat = fmod($jumlah_asli, $faktor_konversi) == 0;

                    return [
                        'id' => $item->id,
                        'tanggal' => $item->created_at->format('Y-m-d'),
                        'nama_obat' => optional($item->stok->drug)->nama_obat ?? 'Tidak Diketahui',
                        'batch' => $item->stok->batch,
                        'satuan' => $is_bulat
                            ? (optional($item->stok->konversiSatuan)->nama_satuan ?? 'Tidak Diketahui')
                            : (optional($item->stok->drug)->satuan_dasar ?? 'Tidak Diketahui'),
                        'jenis_mutasi' => $item->jenis_mutasi,
                        'jumlah' => $is_bulat ? $jumlah_konversi : $jumlah_asli,
                    ];
                }),
                'pagination' => [
                    'total' => $mutasi_stok->total(),
                    'current_page' => $mutasi_stok->currentPage(),
                    'last_page' => $mutasi_stok->lastPage(),
                    'prev_page_url' => $mutasi_stok->previousPageUrl(),
                    'next_page_url' => $mutasi_stok->nextPageUrl(),
                ]
            ]);
        }

        return view('laporan.stok', compact('mutasi_stok'));
    }

    public function laporanObatKadaluarsa(Request $request)
    {
        $query = Stok::with('drug', 'konversiSatuan')
            ->whereDate('tanggal_kadaluarsa', '<', now());

        if ($request->has('search') && $request->search) {
            $query->whereHas('drug', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->search . '%');
            });
        }

        $kadaluarsa = $query->orderBy('tanggal_kadaluarsa', 'asc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => $kadaluarsa->map(function ($item) {
                    $faktor_konversi = optional($item->konversiSatuan)->jumlah_satuan_terkecil ?? 1;

                    return [
                        'id' => $item->id,
                        'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa,
                        'nama_obat' => optional($item->drug)->nama_obat ?? 'Tidak Diketahui',
                        'batch' => $item->batch,
                        'satuan' => optional($item->konversiSatuan)->nama_satuan ?? 'Tidak Diketahui',
                        'stok_gudang' => $item->stok_gudang / $faktor_konversi,
                        'stok_etalase' => $item->stok_etalase / $faktor_konversi,
                        'stok_sisa_eceran' => $item->stok_sisa_eceran / $faktor_konversi,
                        'stok_apotik_cabang' => $item->stok_apotik_cabang / $faktor_konversi,
                    ];
                }),
                'pagination' => [
                    'total' => $kadaluarsa->total(),
                    'current_page' => $kadaluarsa->currentPage(),
                    'last_page' => $kadaluarsa->lastPage(),
                    'prev_page_url' => $kadaluarsa->previousPageUrl(),
                    'next_page_url' => $kadaluarsa->nextPageUrl(),
                ]
            ]);
        }

        return view('laporan.obat_kadaluarsa', compact('kadaluarsa'));
    }
}
