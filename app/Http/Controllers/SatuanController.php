<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $satuan = Satuan::where('nama_satuan', 'like', "%$search%")
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
            'nama_satuan' => 'required|unique:satuans,nama_satuan',
            'keterangan' => 'nullable|string',
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
    // Validate the request
    $validatedData = $request->validate([
        'nama_satuan' => 'required|unique:satuans,nama_satuan,' . $id, // Use $id to ignore the current record
        'keterangan' => 'nullable|string',
    ]);

    // Find the Satuan by ID
    $satuan = Satuan::find($id);
    if (!$satuan) {
        return redirect()->route('data-satuan.index')->with('error', 'Data satuan tidak ditemukan.');
    }

    // Update the Satuan
    $satuan->update($validatedData);

    // Redirect to the index page with success message
    return redirect()->route('data-satuan.index')->with('success', 'Data berhasil diperbarui.');
}



public function destroy($id)
{
    $drug = Satuan::findOrFail($id);
    $drug->delete();

    return response()->json([
        'message' => 'Data berhasil dihapus.'
    ]);
}



}
