<?php

namespace App\Http\Controllers;

use App\Models\DrugsModel;
use App\Models\KonversiSatuan;
use App\Models\MutasiStok;
use App\Models\Stok;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class StokController extends Controller
{
    public function stokGudang(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori');

        $stok_gudang = Stok::query()
            ->with(['drug', 'konversiSatuan'])
            ->whereDate('tanggal_kadaluarsa', '>=', now())
            ->when($search, function ($query, $search) {
                return $query->whereHas('drug', function ($subQuery) use ($search) {
                    $subQuery->where('nama_obat', 'like', "%{$search}%");
                });
            })
            ->when($kategori, function ($query, $kategori) {
                return $query->whereHas('drug', function ($subQuery) use ($kategori) {
                    $subQuery->where('kategori_obat', $kategori);
                });
            })
            ->orderBy('batch', 'asc')
            ->paginate(10)
            ->appends($request->except('page'));

        $kategoris = DrugsModel::select('kategori_obat')->distinct()->pluck('kategori_obat');

        if ($request->ajax()) {
            return response()->json([
                'data' => $stok_gudang->items(),
                'pagination' => [
                    'prev_page_url' => $stok_gudang->previousPageUrl(),
                    'next_page_url' => $stok_gudang->nextPageUrl(),
                    'last_page' => $stok_gudang->lastPage(),
                ],
            ]);
        }

        return view('stok.gudang', compact('stok_gudang', 'kategoris'));
    }

    public function stokEtalase(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori');

        $stok_etalase = Stok::query()
            ->with(['drug', 'konversiSatuan'])
            ->whereDate('tanggal_kadaluarsa', '>=', now())
            ->when($search, function ($query, $search) {
                return $query->whereHas('drug', function ($subQuery) use ($search) {
                    $subQuery->where('nama_obat', 'like', "%{$search}%");
                });
            })
            ->when($kategori, function ($query, $kategori) {
                return $query->whereHas('drug', function ($subQuery) use ($kategori) {
                    $subQuery->where('kategori_obat', $kategori);
                });
            })
            ->orderBy('batch', 'asc')
            ->paginate(10)
            ->appends($request->except('page'));

        $kategoris = DrugsModel::select('kategori_obat')->distinct()->pluck('kategori_obat');

        if ($request->ajax()) {
            return response()->json([
                'data' => $stok_etalase->items(),
                'pagination' => [
                    'prev_page_url' => $stok_etalase->previousPageUrl(),
                    'next_page_url' => $stok_etalase->nextPageUrl(),
                    'last_page' => $stok_etalase->lastPage(),
                ],
            ]);
        }

        return view('stok.etalase', compact('stok_etalase', 'kategoris'));
    }

    public function pemindahanStok()
    {
        $drugs = DrugsModel::query()
            ->with('stokBarang', 'konversiSatuan')
            ->get();

        return view('stok.pemindahan_stok', compact('drugs'));
    }

    public function updateStok(Request $request)
    {
        $request->validate([
            'obat_id' => 'required|exists:stok,obat_id',
            'batch'     => 'required|exists:stok,batch',
            'amount' => 'required|integer|min:1',
            'direction' => 'required|in:gudang_to_etalase,etalase_to_gudang,gudang_to_cabang',
        ]);

        $stock = Stok::where('batch', $request->batch)->first();

        if (!$stock) {
            return back()->with('error', 'Stok tidak ditemukan.');
        }

        $konversiSatuan = KonversiSatuan::where('id', $stock->konversi_satuan_id)->first();
        $jumlahSatuanTerkecil = $konversiSatuan->jumlah_satuan_terkecil;
        $stockUpdate = $request->amount * $jumlahSatuanTerkecil;

        $jenis_mutasi = '';

        if ($request->direction == 'gudang_to_etalase') {
            if ($stock->stok_gudang < $stockUpdate) {
                return back()->with('error', 'Stok gudang tidak mencukupi.');
            }
            $stock->stok_gudang -= $stockUpdate;
            $stock->stok_etalase += $stockUpdate;
            $jenis_mutasi = 'gudang-ke-etalase';
        } elseif ($request->direction == 'etalase_to_gudang') {
            if ($stock->stok_etalase < $stockUpdate) {
                return back()->with('error', 'Stok etalase tidak mencukupi.');
            }
            $stock->stok_etalase -= $stockUpdate;
            $stock->stok_gudang += $stockUpdate;
            $jenis_mutasi = 'etalase-ke-gudang';
        } elseif ($request->direction == 'gudang_to_cabang') {
            if ($stock->stok_gudang < $stockUpdate) {
                return back()->with('error', 'Stok gudang tidak mencukupi.');
            }
            $stock->stok_gudang -= $stockUpdate;
            $stock->stok_apotik_cabang += $stockUpdate;
            $jenis_mutasi = 'gudang-ke-apotik_cabang';
        }

        $stock->save();

        // Catat mutasi stok
        $mutasi = MutasiStok::create([
            'stok_id'     => $stock->id,
            'jenis_mutasi' => $jenis_mutasi,
            'jumlah'      => $stockUpdate,
        ]);

        // Ambil semua admin dan owner untuk menerima notifikasi
        $admins = User::whereIn('role', ['admin', 'owner'])->get();

        // Tentukan pesan & ikon berdasarkan jenis mutasi
        if ($mutasi->jenis_mutasi === 'gudang-ke-etalase') {
            $message = "Stok barang sebanyak {$request->amount} telah dipindahkan dari Gudang ke Etalase.";
            $icon = 'bi bi-arrow-up-circle text-primary';
        } elseif ($mutasi->jenis_mutasi === 'etalase-ke-gudang') {
            $message = "Stok barang sebanyak {$request->amount} telah dipindahkan dari Etalase ke Gudang.";
            $icon = 'bi bi-arrow-down-circle text-warning';
        } elseif ($jenis_mutasi === 'gudang-ke-apotik_cabang') {
            $message = "Stok barang sebanyak {$request->amount} telah dipindahkan dari Gudang ke Apotik Cabang.";
            $icon = 'bi bi-truck text-success';
        } else {
            $message = "Terjadi perubahan stok barang.";
            $icon = 'bi bi-info-circle text-secondary';
        }

        // Kirim notifikasi
        Notification::send($admins, new CustomNotification($message, $icon));

        return back()->with('success', 'Pemindahan stok berhasil.');
    }
}
