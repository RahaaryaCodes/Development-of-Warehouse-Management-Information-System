<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $satuan = Satuan::where('nama_satuan', 'like', "%$search%")
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'data' => $satuan->items(),
                'pagination' => [
                    'prev_page_url' => $satuan->previousPageUrl(),
                    'next_page_url' => $satuan->nextPageUrl(),
                    'last_page' => $satuan->lastPage(),
                ],
            ]);
        }
        return view('satuan.index', compact('satuan'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'konversi' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'nama_satuan' => 'required|string|unique:satuans,nama_satuan,NULL,id,keterangan,' . $request->keterangan,
        ]);

        Satuan::create($request->all());

        return redirect()->route('data-satuan.index')->with('added', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $satuan = Satuan::findOrFail($id);
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);
        $validated = $request->validate([
            'nama_satuan' => 'required|string',
            'konversi' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        // dd($satuan->all());

        $satuan->update($validated);

        return redirect()->route('data-satuan.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $satuan = Satuan::findOrFail($id);
        $satuan->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus.'
        ]);
    }

    public function list()
    {
        $satuan = Satuan::all();
        return response()->json($satuan);
    }
}
