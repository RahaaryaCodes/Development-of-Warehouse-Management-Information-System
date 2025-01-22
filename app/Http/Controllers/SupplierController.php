<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
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
        return redirect()->route('data-supplier.index')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'keterangan' => 'nullable|string'
        ]);

        $supplier->update($validated);
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diupdate');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('data-supplier.index')->with('success', 'Supplier berhasil dihapus');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $suppliers = Supplier::where('nama_supplier', 'like', "%{$query}%")
                            ->orWhere('telepon', 'like', "%{$query}%")
                            ->get();
        return response()->json(['data' => $suppliers]);
    }
}
