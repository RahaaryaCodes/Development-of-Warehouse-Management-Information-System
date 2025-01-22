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
        $kategoris = Kategori::all();
        $drugs = DrugsModel::all();
        return view('drugs.index', compact('kategoris','drugs'));
    }

    // Menampilkan form untuk membuat drug baru
    public function create()
    {
        return view('drugs.create');
    }

    public function store(Request $request)
    {
        // Validasi data obat
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

        // Redirect ke halaman daftar obat
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


    // Menghapus drug
    public function destroy($id)
    {
        $drug = DrugsModel::findOrFail($id);
        $drug->delete();
    
        return redirect()->route('data-obat.index')->with('success', 'Data obat berhasil dihapus.');
    }
    public function search(Request $request)
{
    $query = $request->input('query');
    $category = $request->input('category');
    
    $drugs = DrugsModel::query()
        ->when($query, function($q) use ($query) {
            return $q->where('nama_obat', 'like', "%{$query}%")
                    ->orWhere('batch', 'like', "%{$query}%");
        })
        ->when($category, function($q) use ($category) {
            return $q->where('kategori_obat', $category);
        })
        ->get();
    
    return response()->json(['data' => $drugs]);
}
    
}
