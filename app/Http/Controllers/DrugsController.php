<?php

namespace App\Http\Controllers;

use App\Exports\DrugTemplateExport;
use App\Exports\ObatExport;
use App\Imports\ObatImport;
use App\Models\DrugsModel;
use App\Models\Golongan;
use App\Models\Kategori;
use App\Models\KonversiSatuan;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DrugsController extends Controller
{
    // Menampilkan semua drugs
    public function index(Request $request)
{
    $search = $request->input('search');
    $kategori = $request->input('kategori');
    $golongan = $request->input('golongan');
    
    // Query for drugs with search, category, and golongan filters
    $drugs = DrugsModel::query()
        ->with('satuan') // Hanya eager load relasi satuan
        ->when($search, function ($query, $search) {
            return $query->where('nama_obat', 'like', "%{$search}%");
        })
        ->when($kategori, function ($query, $kategori) {
            return $query->where('kategori_obat', $kategori);
        })
        ->when($golongan, function ($query, $golongan) {
            return $query->where('golongan_obat', $golongan);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->except('page'));

    // Get unique categories from drugs
    $kategoris = DrugsModel::select('kategori_obat')->distinct()->pluck('kategori_obat');
    // Get just the nama_golongan values from Golongan model
    $golongans = Golongan::orderBy('nama_golongan')->pluck('nama_golongan');

    if ($request->ajax()) {
        return response()->json([
            'data' => $drugs->items(),
            'pagination' => [
                'prev_page_url' => $drugs->previousPageUrl(),
                'next_page_url' => $drugs->nextPageUrl(),
                'last_page' => $drugs->lastPage(),
            ]
        ]);
    }

    return view('drugs.index', compact('drugs', 'kategoris', 'golongans', 'search', 'kategori', 'golongan'));
}


    // Menampilkan form untuk membuat drug baru
    public function create()
    {
        $satuans = Satuan::orderBy('nama_satuan')->get();
        $kategoris = Kategori::all();
        $golongans = Golongan::all(); // Ambil semua golongan dari database

        return view('drugs.create', compact('kategoris', 'satuans', 'golongans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:100',
            'kategori_obat' => 'required|string|exists:kategoris,nama_kategori',
            'golongan_obat' => 'required|string', // Tambahkan validasi golongan
            'jenis_obat' => 'required|string|max:50',
            'satuan_dasar' => 'required',
            'nama_satuan.*' => 'required|exists:satuans,nama_satuan',
            'jumlah_satuan_terkecil.*' => 'required|integer|min:1',
            'stock_minimum' => 'required|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Simpan data obat ke tabel drugs
            $obat = DrugsModel::create([
                'nama_obat' => $request->nama_obat,
                'kategori_obat' => $request->kategori_obat,
                'golongan_obat' => $request->golongan_obat, // Simpan golongan obat
                'jenis_obat' => $request->jenis_obat,
                'satuan_dasar' => $request->satuan_dasar,
                'stock_minimum' => $request->stock_minimum
            ]);

            // Simpan konversi satuan jika ada
            if ($request->has('nama_satuan')) {
                foreach ($request->nama_satuan as $index => $satuan_id) {
                    KonversiSatuan::create([
                        'obat_id' => $obat->id,
                        'nama_satuan' => $satuan_id,
                        'jumlah_satuan_terkecil' => $request->jumlah_satuan_terkecil[$index],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('data-obat.index')->with('success', 'Data obat berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan form untuk edit drug
    public function edit($id)
{
    $drug = DrugsModel::findOrFail($id);
    $kategoris = Kategori::all();
    
    // Get full Satuan objects with all properties (nama_satuan, konversi, keterangan)
    $satuans = Satuan::orderBy('nama_satuan')->get();
    
    $golongans = Golongan::all();
    $konversis = KonversiSatuan::where('obat_id', $id)->get();

    return view('drugs.edit', compact('drug', 'kategoris', 'satuans', 'konversis', 'golongans'));
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:100',
            'kategori_obat' => 'required|string|exists:kategoris,nama_kategori',
            'golongan_obat' => 'required|string', // Tambahkan validasi golongan
            'jenis_obat' => 'required|string|max:50',
            'satuan_dasar' => 'required',
            'nama_satuan.*' => 'required|exists:satuans,nama_satuan',
            'jumlah_satuan_terkecil.*' => 'required|integer|min:1',
            'stock_minimum' => 'required|min:0',
        ]);

        DB::beginTransaction();

        try {
            $drug = DrugsModel::findOrFail($id);

            $drug->update([
                'nama_obat' => $request->nama_obat,
                'kategori_obat' => $request->kategori_obat,
                'golongan_obat' => $request->golongan_obat, 
                'jenis_obat' => $request->jenis_obat,
                'satuan_dasar' => $request->satuan_dasar,
                'stock_minimum' => $request->stock_minimum
            ]);

            // Hapus semua konversi satuan lama untuk obat ini
            KonversiSatuan::where('obat_id', $id)->delete();

            // Simpan ulang satuan tambahan jika ada
            if ($request->has('nama_satuan')) {
                foreach ($request->nama_satuan as $index => $satuan_id) {
                    KonversiSatuan::create([
                        'obat_id' => $id,
                        'nama_satuan' => $satuan_id,
                        'jumlah_satuan_terkecil' => $request->jumlah_satuan_terkecil[$index],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('data-obat.index')->with('success', 'Data obat berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menghapus data drug
    public function destroy($id)
    {
        $drug = DrugsModel::findOrFail($id);
        $drug->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus.'
        ]);
    }

    // Fungsi pencarian dengan Ajax
    public function search(Request $request)
    {
        $query = DrugsModel::query();

        if ($request->filled('search')) {
            $query->where('nama_obat', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_obat', $request->kategori);
        }

        $data = $query->paginate(10);

        return response()->json([
            'data' => $data->items(),
            'links' => [
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl()
            ],
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    public function downloadTemplate()
    {
        $headers = ["nama_obat", "kategori_obat", "jenis_obat", "satuan_dasar", "golongan_obat", "stock_minimum"];

        return Excel::download(new DrugTemplateExport($headers), 'template-obat.xlsx');
    }

    public function importExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    try {
        Excel::import(new ObatImport, $request->file('file'));

        return response()->json([
            'status' => 'success',
            'message' => 'Data obat berhasil diimpor.',
        ]);
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();

        $errors = [];
        foreach ($failures as $failure) {
            $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Validasi gagal.',
            'errors' => $errors,
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat impor: ' . $e->getMessage(),
        ], 500);
    }
}
public function export()
{
    $search = request('search');
    $kategori = request('kategori');
    $golongan = request('golongan');
    $format = request('format', 'xlsx');
    
    $filename = 'data-obat.' . $format;
    
    return Excel::download(new ObatExport($search, $kategori, $golongan), $filename);
}
}
