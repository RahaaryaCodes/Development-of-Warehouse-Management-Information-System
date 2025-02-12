<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Penerimaan;
use App\Models\DetailPenerimaan;
use App\Models\DrugsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    public function index()
    {
        // Get orders that are ready for receiving (status = "Dikirim")
        $penerimaans = Penerimaan::with(['pemesanan.supplier'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('penerimaan.index', compact('penerimaans'));
    }

    public function createFromOrder($pemesanan_id)
    {
        $pemesanan = Pemesanan::with(['supplier', 'detailPemesanan'])
            ->findOrFail($pemesanan_id);

        // Only allow creation if status is "Dikirim"
        if ($pemesanan->status !== 'Dikirim') {
            return redirect()->back()->with('error', 'Pemesanan belum dikirim');
        }

        return view('penerimaan.create', compact('pemesanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pemesanan_id' => 'required|exists:pemesanans,id',
            'tanggal_penerimaan' => 'required|date',
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.jumlah_diterima' => 'required|numeric|min:0',
            'items.*.kondisi' => 'required|in:Baik,Rusak',
            'items.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $pemesanan = Pemesanan::findOrFail($request->pemesanan_id);
            $supplier = $pemesanan->supplier;

            // Create penerimaan record
            $penerimaan = Penerimaan::create([
                'pemesanan_id' => $pemesanan->id,
                'tanggal_penerimaan' => $request->tanggal_penerimaan,
                'ppn' => $supplier->ppn ?? 0,
                'status' => 'Menunggu Konfirmasi'
            ]);

            // Process each received item
            foreach ($request->items as $item) {
                DetailPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'detail_pemesanan_id' => $item['id'],
                    'jumlah_diterima' => $item['jumlah_diterima'],
                    'kondisi' => $item['kondisi'],
                    'keterangan' => $item['keterangan'] ?? null
                ]);
            }

            // Update pemesanan status
            $pemesanan->update(['status' => 'Diterima']);

            DB::commit();
            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan berhasil dicatat');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirm($id)
    {
        DB::beginTransaction();
        try {
            $penerimaan = Penerimaan::with(['detailPenerimaan.detailPemesanan'])->findOrFail($id);
            
            foreach ($penerimaan->detailPenerimaan as $detail) {
                $orderDetail = $detail->detailPemesanan;
                
                // Only process items in good condition
                if ($detail->kondisi !== 'Baik') {
                    continue;
                }

                // Try to find existing drug
                $drug = DrugsModel::where('nama_obat', $orderDetail->nama_obat)
                    ->first();

                if ($drug) {
                    // Update existing drug stock
                    $drug->stok += $detail->jumlah_diterima;
                    $drug->save();
                } else {
                    // Create new drug entry
                    DrugsModel::create([
                        'nama_obat' => $orderDetail->nama_obat,
                        'stok' => $detail->jumlah_diterima,
                        'zat_aktif' => $orderDetail->zat_aktif ?? null,
                        'bentuk_sediaan' => $orderDetail->bentuk_sediaan ?? null,
                        'satuan' => $orderDetail->satuan ?? 'Unit',
                        // Add other necessary fields based on your drugs table
                    ]);
                }
            }

            // Update penerimaan status
            $penerimaan->update(['status' => 'Selesai']);
            $penerimaan->pemesanan->update(['status' => 'Selesai']);

            DB::commit();
            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan berhasil dikonfirmasi');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}