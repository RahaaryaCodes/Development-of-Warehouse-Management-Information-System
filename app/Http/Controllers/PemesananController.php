<?php

namespace App\Http\Controllers;

use App\Models\DetailPemesanan;
use App\Models\DrugsModel;
use App\Models\Pemesanan;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pemesanan::with('supplier');

        // Filter berdasarkan pencarian nama supplier
        if ($request->has('search') && $request->search) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('nama_supplier', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan jenis surat, jika ada
        if ($request->has('jenis_surat') && $request->jenis_surat) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        // Menyortir data berdasarkan created_at
        $pemesanans = $query->orderBy('created_at', 'desc')->paginate(10);

        // Jika permintaan AJAX
        if ($request->ajax()) {
            return response()->json([
                'data' => $pemesanans->isEmpty() ? [] : $pemesanans->map(function ($pemesanan) {
                    return [
                        'id' => $pemesanan->id,
                        'tanggal_pemesanan' => $pemesanan->tanggal_pemesanan,
                        'supplier' => optional($pemesanan->supplier)->nama_supplier ?? 'Supplier Tidak Tersedia',
                        'jenis_surat' => $pemesanan->jenis_surat,
                        'total_harga' => $pemesanan->total_harga,
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

        // Untuk tampilan biasa
        return view('pemesanan.index', compact('pemesanans'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mengambil data supplier dan obat
        $suppliers = Supplier::all();
        $drugs = DrugsModel::all();
        return view('pemesanan.create', compact('suppliers', 'drugs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'supplier_id' => 'required',
            'jenis_surat' => 'required',
            'tanggal_pemesanan' => 'required|date',
            'total_harga' => 'required|numeric',  // Validasi untuk total harga
        ]);

        // Membuat pemesanan baru
        $pemesanan = Pemesanan::create($request->only(['supplier_id', 'jenis_surat', 'tanggal_pemesanan', 'total_harga']));

        // Menyimpan detail pemesanan (obat yang dipesan)
        foreach ($request->obat_id as $key => $obat_id) {
            DetailPemesanan::create([
                'pemesanan_id' => $pemesanan->id,
                'obat_id' => $obat_id,
                'jumlah' => $request->jumlah[$key],
                'harga_satuan' => $request->harga_satuan[$key],
                'total_harga' => $request->jumlah[$key] * $request->harga_satuan[$key]
            ]);
        }

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Menampilkan data pemesanan berdasarkan ID jika diperlukan (kosong untuk saat ini)
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Form edit (kosong untuk saat ini)
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Update pemesanan (kosong untuk saat ini)
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Menghapus pemesanan berdasarkan ID (kosong untuk saat ini)
    }
}
