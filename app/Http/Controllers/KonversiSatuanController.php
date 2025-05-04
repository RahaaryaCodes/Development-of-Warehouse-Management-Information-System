<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KonversiSatuan;
use App\Models\Satuan;

class KonversiSatuanController extends Controller
{
    function index(Request $request)
    {
        $search = $request->query('search');

        $konversiSatuan = KonversiSatuan::with(['satuanDari', 'satuanKe'])
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('satuanDari', function ($q) use ($search) {
                    $q->where('nama_satuan', 'like', "%{$search}%");
                })
                    ->orWhereHas('satuanKe', function ($q) use ($search) {
                        $q->where('nama_satuan', 'like', "%{$search}%");
                    });
            })->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => $konversiSatuan->items(),
                'pagination' => [
                    'current_page' => $konversiSatuan->currentPage(),
                    'last_page' => $konversiSatuan->lastPage(),
                    'prev_page_url' => $konversiSatuan->previousPageUrl(),
                    'next_page_url' => $konversiSatuan->nextPageUrl(),
                    'per_page' => $konversiSatuan->perPage()
                ]
            ]);
        }

        return view('konversi-satuan.index');
    }


    public function create()
    {
        $satuan = Satuan::all();
        return view('konversi-satuan.create', compact('satuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'satuan_dari' => 'required|exists:satuans,id',
            'satuan_ke' => 'required|exists:satuans,id',
            'jumlah' => 'required|numeric|min:0.0001'
        ]);

        KonversiSatuan::create($request->all());
        return redirect()->route('konversi-satuan.index')->with('success', 'Konversi satuan berhasil ditambahkan!');
    }
    public function edit($id)
    {
        $konversi = KonversiSatuan::findOrFail($id);
        $satuan = Satuan::all();
        return view('konversi-satuan.edit', compact('konversi', 'satuan'));
    }

    public function update(Request $request, KonversiSatuan $konversiSatuan)
    {
        $request->validate([
            'satuan_dari' => 'required|exists:satuans,id',
            'satuan_ke' => 'required|exists:satuans,id',
            'jumlah' => 'required|numeric|min:0.0001'
        ]);

        $konversiSatuan->update($request->all());
        return redirect()->route('konversi-satuan.index')
            ->with('success', 'Konversi satuan berhasil diperbarui!');
    }

    public function destroy(KonversiSatuan $konversiSatuan)
    {
        $konversiSatuan->delete();
        return response()->json(['message' => 'Konversi satuan berhasil dihapus!']);
    }
}
