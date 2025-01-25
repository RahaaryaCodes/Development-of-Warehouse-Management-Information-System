<?php

namespace App\Http\Controllers;

use App\Models\DrugsModel;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DrugsController extends Controller
{
    // Menampilkan semua drugs
    public function index(Request $request)
    {
        $search = $request->input('search', '');  // Ambil input search
        $kategori = $request->input('kategori', '');  // Ambil kategori filter
        
        // Query untuk mengambil data obat berdasarkan pencarian, kategori, dan urutan descending berdasarkan 'created_at'
        $drugs = DrugsModel::when($search, function ($query, $search) {
                return $query->where('nama_obat', 'like', '%' . $search . '%');
            })
            ->when($kategori, function ($query, $kategori) {
                return $query->where('kategori_obat', $kategori);
            })
            ->paginate(10);
        
        $kategoris = Kategori::pluck('nama_kategori'); // Ambil kategori
        
        return view('drugs.index', compact('drugs', 'kategoris', 'search', 'kategori'));
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
    $drug = DrugsModel::find($id);

    if (!$drug) {
        return redirect()->route('data-obat.index')->with('error', 'Data obat tidak ditemukan.');
    }

    return view('drugs.edit', compact('drug'));
}


    // Mengupdate data drug
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'batch' => 'required',
            'nama_obat' => 'required',
            'kategori_obat' => 'required',
            'jenis_obat' => 'required',
            'satuan' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|numeric',
            'stok_minimum' => 'required|numeric',
            'tanggal_kadaluarsa' => 'required|date',
        ]);

        $drug = DrugsModel::find($id);

        if (!$drug) {
            return redirect()->route('data-obat.index')->with('error', 'Data obat tidak ditemukan.');
        }

        $drug->update($validatedData);

        return redirect()->route('data-obat.index')->with('success', 'Data obat berhasil diperbarui');
    }

    // Menghapus data drug
    public function destroy($id)
    {
        $drug = DrugsModel::findOrFail($id);
        $drug->delete();

        return redirect()->route('data-obat.index')->with('success', 'Data berhasil dihapus.');
    }

    // Fungsi pencarian dengan Ajax
    public function search(Request $request)
{
    $query = DrugsModel::query();

    // Filter berdasarkan pencarian
    if ($request->has('search') && !empty($request->search)) {
        $query->where('nama_obat', 'like', '%' . $request->search . '%');
    }

    // Filter berdasarkan kategori
    if ($request->has('kategori') && !empty($request->kategori)) {
        $query->where('kategori_obat', $request->kategori);
    }

    // Paginate data hasil pencarian
    $drugs = $query->paginate(10);

    // Menambahkan query string ke pagination
    $drugs->appends([
        'search' => $request->search,
        'kategori' => $request->kategori
    ]);

    return response()->json([
        'data' => $drugs->items(),
        'links' => [
            'prev' => $drugs->previousPageUrl(),
            'next' => $drugs->nextPageUrl(),
        ],
        'current_page' => $drugs->currentPage(),
        'total_pages' => $drugs->lastPage(),
    ]);
}



}
