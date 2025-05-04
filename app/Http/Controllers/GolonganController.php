<?php

namespace App\Http\Controllers;

use App\Models\Golongan;
use Illuminate\Http\Request;

class GolonganController extends Controller
{
        public function index(Request $request)
        {
            $search = $request->get('search');
            $golongans = Golongan::where('nama_golongan', 'like', "%$search%")
                ->orderBy('updated_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();
        
            if ($request->ajax()) {
                return response()->json([
                    'data' => $golongans->items(),
                    'pagination' => [
                        'total' => $golongans->total(),
                        'per_page' => $golongans->perPage(),
                        'current_page' => $golongans->currentPage(),
                        'last_page' => $golongans->lastPage()
                    ]
                ]);
            }
        
            return view('golongan.index', compact('golongans'));
        }

    public function create()
    {
        return view('golongan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_golongan' => 'required|string|max:255|unique:golongans',
            'keterangan' => 'nullable|string'
        ]);

        Golongan::create($validated);
        return redirect()->route('data-golongan.index')->with('success', 'Golongan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $golongan = Golongan::find($id);
        return view('golongan.edit', compact('golongan'));
    }

    public function update(Request $request, $id)
    {
        $golongan = Golongan::findOrFail($id);
        $validated = $request->validate([
            'nama_golongan' => 'required|string|max:255|unique:golongans,nama_golongan,' . $golongan->id,
            'keterangan' => 'nullable|string'
        ]);

        $golongan->update($validated);
        return redirect()->route('data-golongan.index')->with('success', 'Golongan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $golongan = Golongan::findOrFail($id);
        $golongan->delete();

        return redirect()->route('data-golongan.index')->with('success', 'Golongan berhasil dihapus');
    }

}
