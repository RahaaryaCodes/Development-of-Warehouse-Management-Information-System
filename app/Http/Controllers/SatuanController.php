<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = Satuan::all();
        return view('satuan.index', compact('satuans'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans',
            'keterangan' => 'nullable|string'
        ]);

        Satuan::create($validated);
        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan');
    }

    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, Satuan $satuan)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan,' . $satuan->id,
            'keterangan' => 'nullable|string'
        ]);

        $satuan->update($validated);
        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diupdate');
    }

    public function destroy(Satuan $satuan)
    {
        $satuan->delete();
        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $satuans = Satuan::where('nama_satuan', 'like', "%{$query}%")->get();
        return response()->json(['data' => $satuans]);
    }
}