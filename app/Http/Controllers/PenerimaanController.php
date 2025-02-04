<?php

namespace App\Http\Controllers;

use App\Models\DrugsModel;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use App\Models\Penerimaan;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    // Menampilkan daftar penerimaan dengan filter pencarian dan jenis surat
    public function index(Request $request)
    {
        $query = Penerimaan::with(['supplier', 'pemesanan']);

        if ($request->has('search') && $request->search) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('nama_supplier', 'like', '%' . $request->search . '%');
            });
        }

        $penerimaans = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => $penerimaans->map(function ($penerimaan) {
                    return [
                        'id' => $penerimaan->id,
                        'no_faktur' => $penerimaan->no_faktur,
                        'tanggal_penerimaan' => $penerimaan->tanggal_penerimaan,
                        'supplier' => optional($penerimaan->supplier)->nama_supplier,
                        'status' => $penerimaan->status,
                    ];
                }),
                'pagination' => [
                    'total' => $penerimaans->total(),
                    'current_page' => $penerimaans->currentPage(),
                    'last_page' => $penerimaans->lastPage(),
                ]
            ]);
        }

        return view('penerimaan.index', compact('penerimaans'));
    }

    public function create()
    {
        // Only show orders with 'Diterima' status
        $pemesanans = Pemesanan::where('status', 'Diterima')
            ->whereDoesntHave('penerimaan')
            ->with('supplier')
            ->get();
        
        if ($pemesanans->isEmpty()) {
            return redirect()->route('penerimaan.index')
                ->with('error', 'Tidak ada pemesanan yang siap untuk diterima.');
        }

        return view('penerimaan.create', compact('pemesanans'));
    }

    public function processReceiving(Request $request)
    {
        DB::beginTransaction();
        try {
            $penerimaan = Penerimaan::create([
                'pemesanan_id' => $request->pemesanan_id,
                'tanggal_penerimaan' => now(),
                'no_faktur' => $request->no_faktur,
                'supplier_id' => $request->supplier_id,
                'status' => 'Pending'
            ]);

            foreach ($request->items as $item) {
                // Check if quantities match
                if ($item['jumlah_diterima'] != $item['jumlah_dipesan']) {
                    throw new \Exception('Jumlah yang diterima tidak sesuai dengan pesanan.');
                }

                // Find or create new drug
                $drug = DrugsModel::firstOrNew(['nama_obat' => $item['nama_obat']]);
                
                if (!$drug->exists) {
                    // Create new drug record
                    $drug->fill([
                        'kategori' => $item['kategori_obat'],
                        'harga_beli' => $item['harga_beli'],
                        'stok' => 0,
                        'batch' => $item['batch'],
                        'tanggal_kadaluarsa' => $item['tanggal_kadaluarsa']
                    ]);
                    $drug->save();
                }

                // Create receiving detail
                $penerimaan->details()->create([
                    'drug_id' => $drug->id,
                    'jumlah_diterima' => $item['jumlah_diterima'],
                    'batch' => $item['batch'],
                    'tanggal_kadaluarsa' => $item['tanggal_kadaluarsa'],
                    'harga_beli' => $item['harga_beli']
                ]);

                // Update drug stock
                $drug->increment('stok', $item['jumlah_diterima']);
            }

            // Update receiving status
            $penerimaan->update(['status' => 'Selesai']);
            
            DB::commit();
            return redirect()->route('penerimaan.index')
                ->with('success', 'Penerimaan barang berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memproses penerimaan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Penerimaan $penerimaan)
    {
        $penerimaan->load(['supplier', 'details.drug', 'pemesanan']);
        return view('penerimaan.show', compact('penerimaan'));
    }

    // Menyimpan data penerimaan baru
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'tanggal_penerimaan' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'jenis_surat' => 'required|string',
            'status' => 'required|string',
        ]);

        // Membuat data penerimaan baru
        Penerimaan::create($request->all());

        // Redirect ke halaman penerimaan dengan pesan sukses
        return redirect()->route('penerimaan.index')->with('success', 'Data penerimaan berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit data penerimaan
    public function edit(Penerimaan $penerimaan)
    {
        $suppliers = Supplier::all(); // Ambil semua data supplier
        return view('penerimaan.edit', compact('penerimaan', 'suppliers'));
    }

    // Mengupdate data penerimaan
    public function update(Request $request, Penerimaan $penerimaan)
    {
        // Validasi input dari form
        $request->validate([
            'tanggal_penerimaan' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'jenis_surat' => 'required|string',
            'status' => 'required|string',
        ]);

        // Update data penerimaan
        $penerimaan->update($request->all());

        // Redirect ke halaman penerimaan dengan pesan sukses
        return redirect()->route('penerimaan.index')->with('success', 'Data penerimaan berhasil diperbarui.');
    }

    // Menghapus data penerimaan
    public function destroy(Penerimaan $penerimaan)
    {
        $penerimaan->delete(); // Menghapus data penerimaan
        return redirect()->route('penerimaan.index')->with('success', 'Data penerimaan berhasil dihapus.');
    }
}
