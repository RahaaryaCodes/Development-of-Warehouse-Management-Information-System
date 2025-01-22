<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
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

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
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
        $query = $request->input('query');
        $kategoris = Kategori::where('nama_kategori', 'like', "%{$query}%")->get();
        return response()->json(['data' => $kategoris]);
    }
}