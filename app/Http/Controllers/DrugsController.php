<?php

namespace App\Http\Controllers;

use App\Models\DrugsModel;
use App\Models\Kategori;
use Illuminate\Http\Request;


class DrugsController extends Controller
{
    // Menampilkan semua drugs
    public function index()
{
    $drugs = DrugsModel::paginate(10);
    $kategoris = Kategori::pluck('nama_kategori'); // Ambil hanya nama kategori
    $startNumber = ($drugs->currentPage() - 1) * $drugs->perPage() + 1;

    // Pastikan format tanggal sudah benar
    $drugs->each(function ($drug) {
        $drug->tanggal_kadaluarsa = \Carbon\Carbon::parse($drug->tanggal_kadaluarsa)->format('d-m-Y');
    });

    return view('drugs.index', compact('drugs', 'startNumber', 'kategoris'));
}

    // Menampilkan form untuk membuat drug baru
    public function create()
    {
        $kategoris = Kategori::all(); // Ambil semua kategori dari database
        return view('drugs.create', compact('kategoris')); // Kirim data ke view
    }

    public function store(Request $request)
{
    $request->validate([
        'batch' => 'required|string',
        'nama_obat' => 'required|string',
        'kategori_obat' => 'required|string', 
        'jenis_obat' => 'required|string',
        'satuan' => 'required|string',
        'dosis' => 'required|string',
        'harga_beli' => 'required|numeric',
        'harga_jual' => 'required|numeric',
        'stok' => 'required|integer',
        'stok_minimum' => 'required|integer',
        'tanggal_kadaluarsa' => 'required|date',
    ]);

    // Simpan data obat
    DrugsModel::create($request->all());

    return redirect()->route('data-obat.index')->with('success', 'Obat berhasil ditambahkan.');
}

    // Menampilkan form untuk edit drug
    public function edit($id)
{
    $drug = DrugsModel::find($id);  // Menggunakan find untuk memuat data berdasarkan ID

    if (!$drug) {
        // Jika data tidak ditemukan, tampilkan error
        return redirect()->route('data-obat.index')->with('error', 'Data obat tidak ditemukan.');
    }

    return view('drugs.edit', compact('drug'));
}


    // Mengupdate data drug
    public function update(Request $request, $id)
{
    // Validasi data yang diterima
    $validatedData = $request->validate([
        'batch' => 'required',
        'nama_obat' => 'required',
        'kategori_obat' => 'required',
        'jenis_obat' => 'required',
        'satuan' => 'required',
        'dosis' => 'required',
        'harga_beli' => 'required|numeric',
        'harga_jual' => 'required|numeric',
        'stok' => 'required|numeric',
        'stok_minimum' => 'required|numeric',
        'tanggal_kadaluarsa' => 'required|date',
    ]);

    // Mencari obat berdasarkan ID
    $drug = DrugsModel::find($id);

    // Pastikan obat ditemukan
    if (!$drug) {
        return redirect()->route('data-obat.index')->with('error', 'Data obat tidak ditemukan.');
    }

    // Update data obat dengan data yang baru
    $drug->update($validatedData);

    // Redirect kembali dengan pesan sukses
    return redirect()->route('data-obat.index')->with('success', 'Data obat berhasil diperbarui');
}


public function destroy($id)
{
    $drug = DrugsModel::findOrFail($id);
    $drug->delete();

    return redirect()->route('data-obat.index')->with('success', 'Data berhasil dihapus.');
}

public function search(Request $request)
{
    if ($request->ajax()) {
        $query = DrugsModel::query();

        // Filter berdasarkan pencarian nama obat
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_obat', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request->has('kategori') && !empty($request->kategori)) {
            $query->where('kategori_obat', $request->kategori);
        }

        // Batasi hasil pencarian menjadi 10 per halaman
        $drugs = $query->paginate(10);

        return response()->json([
            'data' => $drugs->items(),
            'links' => [
                'next' => $drugs->nextPageUrl(),
                'prev' => $drugs->previousPageUrl()
            ]
        ]);
    }

    return response()->json(['error' => 'Invalid request'], 400);
}



}