<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $suppliers = Supplier::query()
            ->when($search, function ($query, $search) {
                return $query->where('nama_supplier', 'like', "%{$search}%");
            })
            ->orderBy('updated_at', 'desc') // Utamakan data yang terakhir di-update
            ->orderBy('created_at', 'desc') // Jika updated_at sama, urutkan berdasarkan waktu pembuatan
            ->paginate(10); // Sesuaikan jumlah item per halaman
            
    
        if ($request->ajax()) {
            return response()->json([
                'data' => $suppliers->items(),
                'pagination' => [
                    'current_page' => $suppliers->currentPage(),
                    'last_page' => $suppliers->lastPage(),
                    'prev_page_url' => $suppliers->previousPageUrl(),
                    'next_page_url' => $suppliers->nextPageUrl(),
                ],
            ]);
        }
    
        return view('supplier.index', compact('suppliers'));
    }
    


    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        // Validasi data yang masuk
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255', // Nama supplier wajib diisi dan tidak lebih dari 255 karakter
            'alamat' => 'required|string', // Alamat wajib diisi
            'telepon' => 'required|numeric|digits_between:10,15', // Telepon wajib diisi dan antara 10 hingga 15 digit
            'email' => 'nullable|email|max:255', // Email boleh kosong, jika ada harus sesuai format
            'keterangan' => 'nullable|string', // Keterangan boleh kosong, jika ada harus berupa string
        ]);
    
        // Simpan data supplier
        Supplier::create($validated);
    
        return redirect()->route('data-supplier.index', ['search' => request('search')])->with('success', 'Supplier berhasil ditambahkan');
    }

    public function edit($id)
{
    $supplier = Supplier::findOrFail($id); 
    return view('supplier.edit', compact('supplier')); 
}


public function update(Request $request, $id)
{
    // Validasi data input
    $validatedData = $request->validate([
        'nama_supplier' => 'required|string|max:255', // Nama supplier wajib diisi dan tidak lebih dari 255 karakter
        'alamat' => 'required|string', // Alamat wajib diisi
        'telepon' => 'required|numeric|digits_between:10,15', // Telepon wajib diisi dan antara 10 hingga 15 digit
        'email' => 'nullable|email|max:255', // Email boleh kosong, jika ada harus sesuai format
        'keterangan' => 'nullable|string', // Keterangan boleh kosong, jika ada harus berupa string
    ]);

    // Cari dan update data supplier
    $supplier = Supplier::find($id);
    if (!$supplier) {
        return redirect()->route('data-supplier.index')->with('error', 'Data supplier tidak ditemukan.');
    }

    $supplier->update($validatedData);

    return redirect()->route('data-supplier.index')->with('success', 'Data supplier berhasil diperbarui');
}


public function destroy($id)
{
    $drug = Supplier::findOrFail($id);
    $drug->delete();

    return response()->json([
        'message' => 'Data berhasil dihapus.'
    ]);
}


public function search(Request $request)
{
    $query = $request->input('search');
    $suppliers = Supplier::where('nama_supplier', 'like', "%{$query}%")
                         ->paginate(10);  // Pagination

    return response()->json([
        'data' => $suppliers->items(),
        'links' => [
            'prev' => $suppliers->previousPageUrl(),
            'next' => $suppliers->nextPageUrl(),
        ],
        'pagination' => [
            'current_page' => $suppliers->currentPage(),
            'last_page' => $suppliers->lastPage(),
            'total_pages' => $suppliers->lastPage(),
        ]
    ]);
}




}
