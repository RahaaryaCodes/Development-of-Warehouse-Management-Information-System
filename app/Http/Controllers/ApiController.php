<?php

namespace App\Http\Controllers;

use App\Models\DrugsModel;
use App\Models\KonversiSatuan;
use App\Models\Pemesanan;
use App\Models\Stok;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getSatuanByObat($obat_id)
    {
        $satuans = KonversiSatuan::where('obat_id', $obat_id)->get();

        return response()->json($satuans);
    }

    public function getBatches($obat_id)
    {
        $batches = Stok::where('obat_id', $obat_id)
            ->with(['drug.konversiSatuan'])
            ->select('id', 'batch', 'stok_gudang', 'stok_etalase', 'tanggal_kadaluarsa', 'obat_id', 'konversi_satuan_id')
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();

        return response()->json($batches);
    }

    public function getObatPemesanan($id)
    {
        $pemesanan = Pemesanan::find($id);
        if (!$pemesanan) {
            return response()->json(['success' => false, 'message' => 'Pemesanan tidak ditemukan.']);
        }

        $detail = $pemesanan->detailPemesanan->first();
        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Detail pemesanan tidak ditemukan.']);
        }

        $obats = json_decode($detail->obats, true);
        $drugs = DrugsModel::all(['id', 'nama_obat']);

        $konversi = [];
        foreach ($drugs as $drug) {
            $konversi[$drug->id] = KonversiSatuan::where('obat_id', $drug->id)
                ->get(['id', 'nama_satuan', 'jumlah_satuan_terkecil']);
        }

        return response()->json([
            'success' => true,
            'obats' => $obats,
            'drugs' => $drugs,
            'konversi' => $konversi
        ]);
    }
}
