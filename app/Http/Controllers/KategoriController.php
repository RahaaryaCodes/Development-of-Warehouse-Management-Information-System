<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    $kategoris = Kategori::where('nama_kategori', 'like', "%$search%")
        ->orderBy('updated_at', 'desc') // Utamakan data yang terakhir di-update
        ->orderBy('created_at', 'desc') // Jika updated_at sama, urutkan berdasarkan waktu pembuatan
        ->paginate(10)
        ->withQueryString();

    if ($request->ajax()) {
        return response()->json([
            'data' => $kategoris->items(),
            'pagination' => [
                'prev_page_url' => $kategoris->previousPageUrl(),
                'next_page_url' => $kategoris->nextPageUrl(),
                'last_page' => $kategoris->lastPage(),
            ],
        ]);
    }

    return view('kategori.index', compact('kategoris'));
}


    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris',
            'keterangan' => 'nullable|string'
        ]);

        Kategori::create($validated);
        return redirect()->route('data-kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kategori = Kategori::find($id);
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori,' . $kategori->id,
            'keterangan' => 'nullable|string'
        ]);
        

        $kategori->update($validated);
        return redirect()->route('data-kategori.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy($id)
{
    $kategori = Kategori::findOrFail($id);
    $kategori->delete();

    return redirect()->route('data-kategori.index')->with('success', 'Kategori berhasil dihapus');
}


    public function search(Request $request)
{
    $query = Kategori::query();

    if ($request->has('search')) {
        $query->where('nama_kategori', 'like', '%' . $request->search . '%');
    }

    if ($request->has('kategori') && $request->kategori) {
        $query->where('nama_kategori', $request->kategori);
    }

    $kategoris = $query->paginate(10);

    return response()->json([
        'data' => $kategoris->items(),
        'current_page' => $kategoris->currentPage(),
        'total_pages' => $kategoris->lastPage(),
        'links' => [
            'prev' => $kategoris->previousPageUrl(),
            'next' => $kategoris->nextPageUrl()
        ]
    ]);
}


}