<?php

namespace App\Http\Controllers;

use App\Models\DetailPemesanan;
use App\Models\DrugsModel;
use App\Models\Pemesanan;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemesanan::with('supplier');

        if ($request->has('search') && $request->search) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('nama_supplier', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('jenis_surat') && $request->jenis_surat) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        $pemesanans = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => $pemesanans->map(function ($pemesanan) {
                    return [
                        'id' => $pemesanan->id,
                        'tanggal_pemesanan' => $pemesanan->tanggal_pemesanan,
                        'supplier' => optional($pemesanan->supplier)->nama_supplier ?? 'Supplier Tidak Tersedia',
                        'jenis_surat' => $pemesanan->jenis_surat,
                        'status' => $pemesanan->status,
                    ];
                }),
                'pagination' => [
                    'total' => $pemesanans->total(),
                    'current_page' => $pemesanans->currentPage(),
                    'last_page' => $pemesanans->lastPage(),
                    'prev_page_url' => $pemesanans->previousPageUrl(),
                    'next_page_url' => $pemesanans->nextPageUrl(),
                ]
            ]);
        }

        return view('pemesanan.index', compact('pemesanans'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $drugs = DrugsModel::all();
        return view('pemesanan.create', compact('suppliers', 'drugs'));
    }

    public function store(Request $request)
{
    $request->validate([
        'surat' => 'required|in:Reguler,Psikotropika,OOT,Prekursor',
        'supplier' => 'required|exists:suppliers,id',
        'tanggal_pemesanan' => 'required|date',
        'items' => 'required|array',
        'catatan' => 'nullable|string'
    ]);

    // Create the main order
    $pemesanan = Pemesanan::create([
        'supplier_id' => $request->supplier,
        'jenis_surat' => $request->surat,
        'tanggal_pemesanan' => $request->tanggal_pemesanan,
        'status' => 'Menunggu Konfirmasi',
        'catatan' => $request->catatan ?? null
    ]);

    // Prepare and save detail orders based on order type
    foreach ($request->items as $index => $item) {
        // Find the drug ID by name
        $drug = DrugsModel::where('nama_obat', $item['nama'])->first();
        
        if (!$drug) {
            // Handle case where drug is not found
            continue;
        }

        $detailData = [
            'pemesanan_id' => $pemesanan->id,
            'obat_id' => $drug->id, // Use the actual drug ID
            'jumlah' => $item['jumlah']
        ];

        // Add additional fields based on order type
        if ($request->surat == 'Reguler') {
            $detailData['keterangan'] = $item['keterangan'] ?? null;
        } else {
            // For Psikotropika, OOT, Prekursor
            $detailData['zat_aktif'] = $item['zat_aktif'] ?? null;
            $detailData['bentuk_sediaan'] = $item['bentuk_sediaan'] ?? null;
            $detailData['satuan'] = $item['satuan'] ?? null;
        }

        DetailPemesanan::create($detailData);
    }

    return redirect()->route('pemesanan-barang.index')
        ->with('success', 'Pemesanan berhasil dibuat.');
}
    

    public function edit($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $suppliers = Supplier::all();
        $drugs = DrugsModel::all();
        return view('pemesanan.edit', compact('pemesanan', 'suppliers', 'drugs'));
    }

    public function update(Request $request, $id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        $request->validate([
            'supplier_id' => 'required',
            'jenis_surat' => 'required',
            'tanggal_pemesanan' => 'required|date',
        ]);

        $pemesanan->update($request->only(['supplier_id', 'jenis_surat', 'tanggal_pemesanan']));

        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil diperbarui.');
    }
    public function show($id)
{
    $pemesanan = Pemesanan::with(['supplier', 'detailPemesanan.obat'])
        ->findOrFail($id);

    return view('pemesanan.show', compact('pemesanan'));
}


public function destroy($id)
{
    $pemesanan = Pemesanan::findOrFail($id);
    $pemesanan->delete();

    return response()->json([
        'message' => 'Data berhasil dihapus.'
    ]);
}
}
