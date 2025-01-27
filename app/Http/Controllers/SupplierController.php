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
            ->orderBy('created_at', 'desc')
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
