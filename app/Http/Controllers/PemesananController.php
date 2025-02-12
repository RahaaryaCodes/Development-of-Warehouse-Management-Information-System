<?php

namespace App\Http\Controllers;

use App\Models\DetailPemesanan;
use App\Models\DrugsModel;
use App\Models\Pemesanan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'catatan' => 'nullable|string',
            'obats' => 'required|array|min:1',
            'obats.*.nama_obat' => 'required|string',
            'obats.*.jumlah' => 'required|numeric|min:1',
        ]);
    
        DB::beginTransaction();
        try {
            // Buat record pemesanan
            $pemesanan = Pemesanan::create([
                'supplier_id' => $request->supplier,
                'jenis_surat' => $request->surat,
                'tanggal_pemesanan' => $request->tanggal_pemesanan,
                'status' => 'Menunggu Konfirmasi',
                'catatan' => $request->catatan
            ]);
    
            // Menyimpan data detail pemesanan
            DetailPemesanan::create([
                'pemesanan_id' => $pemesanan->id,
                'obats' => json_encode($request->obats), // Mengonversi obats ke JSON
                'keterangan' => $request->catatan
            ]);
    
            DB::commit();
            return redirect()->route('pemesanan-barang.index')
                ->with('success', 'Pemesanan berhasil dibuat.');
    
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
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
    $pemesanan = Pemesanan::find($id);
    $detail = $pemesanan->detailPemesanan->first(); // Get the first detail
    $obats = json_decode($detail->obats, true); // Decode the JSON string into an array

    return view('pemesanan.show', compact('pemesanan', 'obats')); // Pass 'obats' to the view
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
