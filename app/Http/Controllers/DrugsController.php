<?php

namespace App\Http\Controllers;

use App\Models\DrugsModel;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DrugsController extends Controller
{
    // Menampilkan semua drugs
    public function index(Request $request)
{
    $search = $request->input('search');
    $kategori = $request->input('kategori');
    // Query untuk mengambil data dengan filter pencarian dan kategori
    $drugs = DrugsModel::query()
        ->when($search, function ($query, $search) {
            return $query->where('nama_obat', 'like', "%{$search}%");
        })
        ->when($kategori, function ($query, $kategori) {
            return $query->where('kategori_obat', $kategori);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->except('page')); // Menambahkan query params selain 'page'

    // Ambil kategori obat yang unik
    $kategoris = DrugsModel::select('kategori_obat')->distinct()->pluck('kategori_obat');

    if ($request->ajax()) {
        return response()->json([
            'data' => $drugs->items(),
            'pagination' => [
                'prev_page_url' => $drugs->previousPageUrl(),
                'next_page_url' => $drugs->nextPageUrl(),
                'last_page' => $drugs->lastPage(),
            ]
        ]);
    }

    return view('drugs.index', compact('drugs', 'kategoris', 'search', 'kategori'));
}

    // Menampilkan form untuk membuat drug baru
    public function create()
    {
        $satuans = Satuan::orderBy('nama_satuan')->get();
        $kategoris = Kategori::all(); // Ambil semua kategori dari database
        return view('drugs.create', compact('kategoris', 'satuans')); // Kirim data ke view
    }

    public function store(Request $request)
{
    $request->validate([
        'batch' => 'required|alpha_num|max:50|unique:drugs,batch',
        'nama_obat' => 'required|string|max:100',
        'kategori_obat' => 'required|string|exists:kategoris,nama_kategori',
        'jenis_obat' => 'required|string|max:50',
        'satuan' => 'required|exists:satuans,nama_satuan',
        'harga_beli' => 'required|numeric|min:0',
        'harga_jual' => 'required|numeric|min:0|gte:harga_beli',
        'stok' => 'required|integer|min:0',
        'stok_minimum' => 'required|integer|min:0',
        'tanggal_kadaluarsa' => 'required|date|after:today',
    ]);

    // Simpan data ke database
    DrugsModel::create($request->all());

    return redirect()->route('data-obat.index')->with('success', 'Data obat berhasil ditambahkan.');
}



    // Menampilkan form untuk edit drug
    public function edit($id)
{
    $drug = DrugsModel::find($id);

    if (!$drug) {
        return redirect()->route('data-obat.index')->with('error', 'Data obat tidak ditemukan.');
    }

    // Pastikan mengambil data kategori dengan benar
    $kategoris = Kategori::all();  // Ganti dengan model yang sesuai untuk kategori
    $satuans = Satuan::orderBy('nama_satuan')->get();
    return view('drugs.edit', compact('drug', 'kategoris', 'satuans'));  // Pastikan 'kategoris' dikirim ke tampilan
}




    // Mengupdate data drug
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'batch' => 'required',
            'nama_obat' => 'required',
            'kategori_obat' => 'required',
            'jenis_obat' => 'required',
            'satuan' => 'required|exists:satuans,nama_satuan',
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

    return response()->json([
        'message' => 'Data berhasil dihapus.'
    ]);
}


    // Fungsi pencarian dengan Ajax
    public function search(Request $request)
{
    $query = DrugsModel::query();

    if ($request->filled('search')) {
        $query->where('nama_obat', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('kategori')) {
        $query->where('kategori_obat', $request->kategori);
    }

    $data = $query->paginate(10);

    return response()->json([
        'data' => $data->items(),
        'links' => [
            'prev' => $data->previousPageUrl(),
            'next' => $data->nextPageUrl()
        ],
        'meta' => [
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
        ]
    ]);
}


}




