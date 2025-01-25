<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $kategoris = Kategori::when($search, function ($query, $search) {
        return $query->where('nama_kategori', 'like', "%{$search}%");
    })->paginate(10);

    if ($request->ajax()) {
        return response()->json([
            'html' => view('kategori.partials.table', compact('kategoris'))->render(),
            'pagination' => $kategoris->links('pagination::bootstrap-5')->render(),
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

    public function destroy(Kategori $kategori)
    {
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