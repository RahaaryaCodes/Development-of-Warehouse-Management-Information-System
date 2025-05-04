<?php

namespace App\Http\Controllers;

use App\Models\DetailPemesanan;
use App\Models\DrugsModel;
use App\Models\KonversiSatuan;
use App\Models\Pemesanan;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemesanan::with('supplier');

        if ($request->has('search') && $request->search) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('nama_supplier', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('jenis_surat') && $request->jenis_surat) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        $pemesanans = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'data' => $pemesanans->map(function ($pemesanan) {
                    return [
                        'id' => $pemesanan->id,
                        'tanggal_pemesanan' => $pemesanan->tanggal_pemesanan,
                        'supplier' => optional($pemesanan->supplier)->nama_supplier ?? 'Supplier Tidak Tersedia',
                        'jenis_surat' => $pemesanan->jenis_surat,
                        'status' => $pemesanan->status,
                    ];
                }),
                'pagination' => [
                    'total' => $pemesanans->total(),
                    'current_page' => $pemesanans->currentPage(),
                    'last_page' => $pemesanans->lastPage(),
                    'prev_page_url' => $pemesanans->previousPageUrl(),
                    'next_page_url' => $pemesanans->nextPageUrl(),
                ]
            ]);
        }

        $drugs = DrugsModel::all();
        $konversis = KonversiSatuan::all();

        return view('pemesanan.index', compact('pemesanans', 'drugs', 'konversis'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $drugs = DrugsModel::all();
        return view('pemesanan.create', compact('suppliers', 'drugs'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $pemesanan = Pemesanan::findOrFail($id);
            $pemesanan->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'surat' => 'required|in:Reguler,Psikotropika,OOT,Prekursor',
            'supplier' => 'required|exists:suppliers,id',
            'tanggal_pemesanan' => 'required|date',
            'catatan' => 'nullable|string',
            'obats' => 'required|array|min:1',
            'obats.*.nama_obat' => 'required|string',
            'obats.*.jumlah' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Buat record pemesanan
            $pemesanan = Pemesanan::create([
                'supplier_id' => $request->supplier,
                'jenis_surat' => $request->surat,
                'tanggal_pemesanan' => $request->tanggal_pemesanan,
                'status' => 'Pending',
                'catatan' => $request->catatan
            ]);

            // Menyimpan data detail pemesanan
            DetailPemesanan::create([
                'pemesanan_id' => $pemesanan->id,
                'obats' => json_encode($request->obats), // Mengonversi obats ke JSON
                'catatan' => $request->catatan

            ]);

            DB::commit();
            return redirect()->route('pemesanan-barang.index')
                ->with('success', 'Pemesanan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $suppliers = Supplier::all();
        $drugs = DrugsModel::all();
        return view('pemesanan.edit', compact('pemesanan', 'suppliers', 'drugs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required',
            'jenis_surat' => 'required',
            'tanggal_pemesanan' => 'required|date',
            'obats' => 'required|array|min:1',
            'obats.*.nama_obat' => 'required|string',
            'obats.*.jumlah' => 'required|numeric|min:1',
        ]);
    
        DB::beginTransaction();
        try {
            // Update pemesanan
            $pemesanan = Pemesanan::findOrFail($id);
            $pemesanan->update([
                'supplier_id' => $request->supplier_id,
                'jenis_surat' => $request->jenis_surat,
                'tanggal_pemesanan' => $request->tanggal_pemesanan,
                'no_surat' => $request->no_surat,
                'catatan' => $request->catatan,
            ]);
    
            // Update or create detailPemesanan
            $detailPemesanan = $pemesanan->detailPemesanan->first();
            
            if ($detailPemesanan) {
                // Get existing obats
                $existingObats = json_decode($detailPemesanan->obats, true) ?: [];
                
                // For debugging, log what we received
              
                
                // Make sure we're preserving uniqueness based on nama_obat
                // Convert arrays to associative arrays with nama_obat as the key
                $obatsMap = [];
                
                // First add existing obats
                foreach ($existingObats as $obat) {
                    if (isset($obat['nama_obat'])) {
                        $obatsMap[$obat['nama_obat']] = $obat;
                    }
                }
                
                // Then add/update with new obats from the form
                foreach ($request->obats as $obat) {
                    if (isset($obat['nama_obat'])) {
                        $obatsMap[$obat['nama_obat']] = $obat;
                    }
                }
                
                // Convert back to sequential array
                $updatedObats = array_values($obatsMap);
                
              
                
                $detailPemesanan->update([
                    'obats' => json_encode($updatedObats),
                    'catatan' => $request->catatan
                ]);
            } else {
                DetailPemesanan::create([
                    'pemesanan_id' => $pemesanan->id,
                    'obats' => json_encode($request->obats),
                    'catatan' => $request->catatan
                ]);
            }
    
            DB::commit();
            return redirect()->route('pemesanan-barang.index')
                ->with('success', 'Pemesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function show($id)
    {
        $pemesanan = Pemesanan::find($id);
        $detail = $pemesanan->detailPemesanan->first(); // Get the first detail
        $obats = json_decode($detail->obats, true); // Decode the JSON string into an array

        return view('pemesanan.show', compact('pemesanan', 'obats', 'detail')); // Pass 'obats' to the view
    }


    public function destroy($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus.'
        ]);
    }

    public function cetakPdf($id)
    {
        $pemesanan = Pemesanan::find($id);
        $detail = $pemesanan->detailPemesanan->first();
        $obats = json_decode($detail->obats, true);

        // Tentukan file view berdasarkan jenis_surat
        $view = match ($pemesanan->jenis_surat) {
            'Psikotropika' => 'pemesanan.surat.cetak_pdf_psikotropika',
            'OOT' => 'pemesanan.surat.cetak_pdf_oot',
            'Prekursor' => 'pemesanan.surat.cetak_pdf_prekursor',
            default => 'pemesanan.surat.cetak_pdf_reguler',
        };

        // Format nama file
        $tanggalSekarang = \Carbon\Carbon::now()->format('dmY');
        $jenisSurat = $pemesanan->jenis_surat === 'OOT'
            ? 'OOT'
            : ucfirst(strtolower($pemesanan->jenis_surat));

        $namaFile = "Laporan_Pemesanan_{$jenisSurat}_{$tanggalSekarang}.pdf";

        $pdf = Pdf::loadView($view, compact('pemesanan', 'obats'))
            ->setPaper('A4', 'portrait');

        return $pdf->download($namaFile);
    }
}
