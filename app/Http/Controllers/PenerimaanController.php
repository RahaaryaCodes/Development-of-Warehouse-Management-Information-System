<?php

namespace App\Http\Controllers;

use App\Models\DetailPenerimaan;
use App\Models\DrugsModel;
use App\Models\Penerimaan;
use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use App\Models\KonversiSatuan;
use App\Models\MutasiStok;
use App\Models\Stok;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PenerimaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penerimaan::with('supplier', 'pemesanan');

        if ($request->has('search') && $request->search) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('nama_supplier', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('jenis_surat') && $request->jenis_surat) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        $penerimaans = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => $penerimaans->map(function ($penerimaan) {
                    return [
                        'id' => $penerimaan->id,
                        'tanggal_diterima' => $penerimaan->tanggal_diterima,
                        'supplier' => optional($penerimaan->supplier)->nama_supplier ?? 'Supplier Tidak Tersedia',
                        'jenis_surat' => $penerimaan->jenis_surat,
                        'status' => $penerimaan->status,
                        'nomor_faktur' => $penerimaan->nomor_faktur,
                        'pemesanan_id' => $penerimaan->pemesanan_id,
                    ];
                }),
                'pagination' => [
                    'total' => $penerimaans->total(),
                    'current_page' => $penerimaans->currentPage(),
                    'last_page' => $penerimaans->lastPage(),
                    'prev_page_url' => $penerimaans->previousPageUrl(),
                    'next_page_url' => $penerimaans->nextPageUrl(),
                ]
            ]);
        }

        return view('penerimaan.index', compact('penerimaans'));
    }

    public function create()
    {
        // Get pending orders that haven't been fully received
        $pemesanans = Pemesanan::with('supplier')
            ->where('status', '!=', 'Selesai')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('penerimaan.create', compact('pemesanans'));
    }

    public function getPemesananDetail($id)
    {
        try {
            $pemesanan = Pemesanan::with('supplier')->findOrFail($id);
            $detailPemesanan = DetailPemesanan::where('pemesanan_id', $id)->first();
            $items = [];

            if ($detailPemesanan && $detailPemesanan->obats) {
                $decodedItems = json_decode($detailPemesanan->obats, true);
                if (is_array($decodedItems)) {
                    $items = $decodedItems;
                }
            }

            return response()->json([
                'pemesanan' => $pemesanan,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'pemesanan_id' => 'required|exists:pemesanans,id',
            'tanggal_diterima' => 'required|date',
            'nomor_faktur' => 'required|string',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.jumlah_diterima' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pemesanan = Pemesanan::with('supplier')->findOrFail($request->pemesanan_id);

            $penerimaan = Penerimaan::create([
                'pemesanan_id' => $request->pemesanan_id,
                'supplier_id' => $pemesanan->supplier_id,
                'jenis_surat' => $pemesanan->jenis_surat,
                'tanggal_diterima' => $request->tanggal_diterima,
                'nomor_faktur' => $request->nomor_faktur,
                'status' => 'Diterima',
                'catatan' => $request->catatan
            ]);

            DetailPenerimaan::create([
                'penerimaan_id' => $penerimaan->id,
                'obats' => json_encode($request->items),
                'catatan' => $request->catatan
            ]);

            $this->updateOrderStatus($request->pemesanan_id, $request->items);
            $this->updateInventory($request->items);

            DB::commit();
            return redirect()->route('penerimaan-barang.index')
                ->with('success', 'Penerimaan barang berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function storeDetailPenerimaan(Request $request)
    {
        try {
            $request->validate([
                'pemesanan_id' => 'required|integer',
                'obat_id' => 'required|array',
                'batch' => 'required|array|unique:detail_penerimaans,batch',
                'no_faktur' => 'required|array',
                'jumlah_terima' => 'required|array',
                'nama_satuan' => 'required|array',
                'harga_beli' => 'required|array',
                'harga_jual' => 'required|array',
                'total_harga' => 'required|array',
                'diskon' => 'nullable|array',
                'ppn' => 'required|array',
                'tanggal_kadaluarsa' => 'required|array',
                'tanggal_diterima' => 'required|array',
                'stok_gudang' => 'nullable|array',
                'stok_etalase' => 'nullable|array',
                'zat_aktif' => 'nullable|array',
                'bentuk_sediaan' => 'nullable|array',
                'keterangan' => 'nullable|array',
                'catatan' => 'nullable|array',
            ]);

            DB::beginTransaction();

            $totalJenisBarang = count($request->obat_id);

            foreach ($request->obat_id as $index => $obatId) {
                DetailPenerimaan::create([
                    'penerimaan_id' => $request->pemesanan_id,
                    'obat_id' => $obatId,
                    'no_faktur' => $request->no_faktur[$index] ?? null,
                    'jumlah_terima' => (int) ($request->jumlah_terima[$index] ?? 0),
                    'jumlah_gudang' => (int) ($request->stok_gudang[$index] ?? 0),
                    'jumlah_etalase' => (int) ($request->stok_etalase[$index] ?? 0),
                    'batch' => $request->batch[$index] ?? null,
                    'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa[$index] ?? null,
                    'tanggal_terima' => $request->tanggal_diterima[$index] ?? null,
                    'harga_beli' => (float) ($request->harga_beli[$index] ?? 0),
                    'harga_jual' => (float) ($request->harga_jual[$index] ?? 0),
                    'diskon' => (float) ($request->diskon[$index] ?? 0),
                    'ppn' => (float) ($request->ppn[$index] ?? 0),
                    'total_harga' => (float) ($request->total_harga[$index] ?? 0),
                    'satuan' => KonversiSatuan::where('id', $request->nama_satuan[$index])->value('nama_satuan') ?? '-',
                    'zat_aktif' => $request->zat_aktif[$index] ?? '-',
                    'bentuk_sediaan' => $request->bentuk_sediaan[$index] ?? '-',
                    'keterangan' => $request->keterangan[$index] ?? '-',
                    'catatan' => $request->catatan[$index] ?? '-',
                ]);

                $konversiSatuan = KonversiSatuan::where('id', $request->nama_satuan[$index])->first();
                if (!$konversiSatuan) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Konversi satuan tidak ditemukan!',
                    ], 400);
                }

                $jumlahSatuanTerkecil = $konversiSatuan->jumlah_satuan_terkecil;
                $stokGudangTerkecil = $request->stok_gudang[$index] * $jumlahSatuanTerkecil;
                $stokEtalaseTerkecil = $request->stok_etalase[$index] * $jumlahSatuanTerkecil;

                $stok = Stok::create([
                    'batch' => $request->batch[$index],
                    'obat_id' => $obatId,
                    'konversi_satuan_id' => $request->nama_satuan[$index],
                    'stok_gudang' => $stokGudangTerkecil,
                    'stok_etalase' => $stokEtalaseTerkecil,
                    'harga_beli' => $request->harga_beli[$index],
                    'harga_jual' => $request->harga_jual[$index],
                    'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa[$index],
                ]);

                MutasiStok::create([
                    'stok_id'     => $stok->id,
                    'jenis_mutasi' => 'pembelian',
                    'jumlah'      => $stokGudangTerkecil + $stokEtalaseTerkecil,
                ]);
            }

            $admins = User::whereIn('role', ['admin', 'owner'])->get();
            $message = "Berhasil melakukan pembelian barang sebanyak {$totalJenisBarang} jenis barang";
            $icon = 'bi bi-cart text-success';

            Notification::send($admins, new CustomNotification($message, $icon));

            $pemesanan = Pemesanan::findOrFail($request->pemesanan_id);
            $pemesanan->status = 'Diterima';
            $pemesanan->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diterima!',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal!',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan!',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    private function updateOrderStatus($pemesananId, $receivedItems)
    {
        $pemesanan = Pemesanan::findOrFail($pemesananId);
        $detailPemesanan = DetailPemesanan::where('pemesanan_id', $pemesananId)->first();
        $orderedItems = json_decode($detailPemesanan->obats, true);

        // Check if all ordered items are fully received
        $fullyReceived = true;

        // Convert received items to a more easily accessible format
        $receivedById = [];
        foreach ($receivedItems as $item) {
            $receivedById[$item['id']] = $item['jumlah_diterima'];
        }

        foreach ($orderedItems as $index => $item) {
            $itemId = $item['id'] ?? $index;
            $orderedQty = $item['jumlah'];
            $receivedQty = isset($receivedById[$itemId]) ? $receivedById[$itemId] : 0;

            if ($receivedQty < $orderedQty) {
                $fullyReceived = false;
                break;
            }
        }

        // Update order status
        if ($fullyReceived) {
            $pemesanan->status = 'Selesai';
        } else {
            $pemesanan->status = 'Sebagian Diterima';
        }

        $pemesanan->save();
    }

    private function updateInventory($items)
    {
        foreach ($items as $item) {
            if (isset($item['jumlah_diterima']) && $item['jumlah_diterima'] > 0) {
                $drug = DrugsModel::find($item['id']);
                if ($drug) {
                    $drug->stok = $drug->stok + $item['jumlah_diterima'];
                    $drug->save();
                }
            }
        }
    }

    public function show($id)
    {
        $penerimaan = Penerimaan::with('supplier', 'pemesanan')->findOrFail($id);
        $detail = DetailPenerimaan::where('penerimaan_id', $id)->first();
        $obats = json_decode($detail->obats, true);

        return view('penerimaan.show', compact('penerimaan', 'obats', 'detail'));
    }

    public function edit($id)
    {
        $penerimaan = Penerimaan::with('supplier', 'pemesanan')->findOrFail($id);
        $detail = DetailPenerimaan::where('penerimaan_id', $id)->first();
        $obats = json_decode($detail->obats, true);

        // Only allow editing if status is not 'Selesai'
        if ($penerimaan->status === 'Selesai') {
            return redirect()->route('penerimaan-barang.index')
                ->with('error', 'Penerimaan yang sudah selesai tidak dapat diedit.');
        }

        return view('penerimaan.edit', compact('penerimaan', 'obats', 'detail'));
    }

    public function update(Request $request, $id)
    {
        $penerimaan = Penerimaan::findOrFail($id);

        // Prevent editing completed receipts
        if ($penerimaan->status === 'Selesai') {
            return redirect()->route('penerimaan-barang.index')
                ->with('error', 'Penerimaan yang sudah selesai tidak dapat diedit.');
        }

        $request->validate([
            'tanggal_diterima' => 'required|date',
            'nomor_faktur' => 'required|string',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.jumlah_diterima' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Get the old receipt details for inventory adjustment
            $oldDetail = DetailPenerimaan::where('penerimaan_id', $id)->first();
            $oldItems = json_decode($oldDetail->obats, true);

            // Update receipt record
            $penerimaan->update([
                'tanggal_diterima' => $request->tanggal_diterima,
                'nomor_faktur' => $request->nomor_faktur,
                'catatan' => $request->catatan
            ]);

            // Update receipt details
            $oldDetail->update([
                'obats' => json_encode($request->items),
                'catatan' => $request->catatan
            ]);

            // Adjust inventory: remove old quantities and add new ones
            $this->adjustInventory($oldItems, $request->items);

            // Update order status
            $this->updateOrderStatus($penerimaan->pemesanan_id, $request->items);

            DB::commit();
            return redirect()->route('penerimaan-barang.index')
                ->with('success', 'Penerimaan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function adjustInventory($oldItems, $newItems)
    {
        // Convert to easily accessible arrays by item ID
        $oldById = [];
        foreach ($oldItems as $item) {
            $oldById[$item['id']] = $item['jumlah_diterima'];
        }

        $newById = [];
        foreach ($newItems as $item) {
            $newById[$item['id']] = $item['jumlah_diterima'];
        }

        // Process all item IDs from both old and new arrays
        $allIds = array_unique(array_merge(array_keys($oldById), array_keys($newById)));

        foreach ($allIds as $id) {
            $oldQty = isset($oldById[$id]) ? $oldById[$id] : 0;
            $newQty = isset($newById[$id]) ? $newById[$id] : 0;
            $adjustmentQty = $newQty - $oldQty;

            if ($adjustmentQty != 0) {
                $drug = DrugsModel::find($id);
                if ($drug) {
                    $drug->stok = $drug->stok + $adjustmentQty;
                    $drug->save();
                }
            }
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $penerimaan = Penerimaan::findOrFail($id);

            // Get the receipt details to adjust inventory
            $detail = DetailPenerimaan::where('penerimaan_id', $id)->first();
            $items = json_decode($detail->obats, true);

            // Remove the received quantities from inventory
            foreach ($items as $item) {
                if (isset($item['jumlah_diterima']) && $item['jumlah_diterima'] > 0) {
                    $drug = DrugsModel::find($item['id']);
                    if ($drug) {
                        $drug->stok = $drug->stok - $item['jumlah_diterima'];
                        $drug->save();
                    }
                }
            }

            // Update the order status
            $pemesanan = Pemesanan::find($penerimaan->pemesanan_id);
            if ($pemesanan) {
                $pemesanan->status = 'Menunggu Penerimaan';
                $pemesanan->save();
            }

            // Delete the receipt details and the receipt
            $detail->delete();
            $penerimaan->delete();

            DB::commit();
            return response()->json([
                'message' => 'Data penerimaan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
