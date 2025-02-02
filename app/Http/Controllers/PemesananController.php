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
            'supplier_id' => 'required',
            'jenis_surat' => 'required',
            'tanggal_pemesanan' => 'required|date',
            'obat_id' => 'required|array',
            'jumlah' => 'required|array',
            'harga_satuan' => 'required|array'
        ]);

        $pemesanan = Pemesanan::create([
            'supplier_id' => $request->supplier_id,
            'jenis_surat' => $request->jenis_surat,
            'tanggal_pemesanan' => $request->tanggal_pemesanan,
            'status' => 'Pending'
        ]);

        $detailPemesanan = [];
        foreach ($request->obat_id as $key => $obat_id) {
            $detailPemesanan[] = [
                'pemesanan_id' => $pemesanan->id,
                'obat_id' => $obat_id,
                'jumlah' => $request->jumlah[$key],
                'harga_satuan' => $request->harga_satuan[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DetailPemesanan::insert($detailPemesanan);

        return redirect()->route('pemesanan-barang.index')->with('success', 'Pemesanan berhasil dibuat.');
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

    public function destroy($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->detailPemesanan()->delete();
        $pemesanan->delete();

        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil dihapus.');
    }
}
