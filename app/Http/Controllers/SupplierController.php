<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search', '');  // Ambil input pencarian
    $suppliers = Supplier::when($search, function ($query, $search) {
        return $query->where('nama_supplier', 'like', '%' . $search . '%');
    })
    ->paginate(10);  // Paginate, pastikan hanya 10 data per halaman
    
    return view('supplier.index', compact('suppliers', 'search'));
}


    

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'keterangan' => 'nullable|string'
        ]);
    
        Supplier::create($validated);
    
        // Redirect dengan mempertahankan query string dan menampilkan data yang terbaru
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
        'nama_supplier' => 'required|string|max:255',
        'alamat' => 'required|string',
        'telepon' => 'required|string|max:15',
        'email' => 'nullable|email|max:255',
        'keterangan' => 'nullable|string',
    ]);

    // Update data supplier dengan data yang sudah divalidasi
    $supplier = Supplier::find($id);
    if (!$supplier) {
        return redirect()->route('data-supplier.index')->with('error', 'Data supplier tidak ditemukan.');
    }

    $supplier->update($validatedData);

    return redirect()->route('data-supplier.index')->with('success', 'Data supplier berhasil diperbarui');
}


    public function destroy($id)
{
    $supplier = Supplier::findOrFail($id);
    $supplier->delete();

    return redirect()->route('data-supplier.index')->with('success', 'Supplier berhasil dihapus.');
}


public function search(Request $request)
{
    // Ambil query pencarian dari request
    $query = $request->input('search');
    
    // Cari data supplier yang sesuai dengan query pencarian
    $suppliers = Supplier::where('nama_supplier', 'like', "%{$query}%")
                         ->paginate(10);  // Tambahkan pagination di live search
    
    // Kembalikan data sebagai response JSON
    return response()->json([
        'data' => $suppliers->items(),  // Data supplier
        'links' => [
            'prev' => $suppliers->previousPageUrl(),
            'next' => $suppliers->nextPageUrl(),
        ],
        'current_page' => $suppliers->currentPage(),
        'total_pages' => $suppliers->lastPage(),
    ]);
}


}
