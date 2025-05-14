<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\DrugsModel;
use App\Models\KonversiSatuan;
use App\Models\MutasiStok;
use App\Models\Penjualan;
use App\Models\Satuan;
use App\Models\Stok;
use App\Models\User;
use App\Notifications\CustomNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PenjualanController extends Controller
{
    public function index()
    {
        $drugs = DrugsModel::whereHas('stok', function ($query) {
            $query->where(function ($q) {
                $q->where('stok_etalase', '>', 0)
                    ->orWhere('stok_gudang', '>', 0)
                    ->orWhere('stok_sisa_eceran', '>', 0);
            })
                ->whereDate('tanggal_kadaluarsa', '>=', now());
        })->with(['stok' => function ($query) {
            $query->whereDate('tanggal_kadaluarsa', '>=', now());
        }])->get();

        $satuans = KonversiSatuan::all();

        return view('penjualan.index', compact('drugs', 'satuans'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'obat_id' => 'required|array',
            'obat_id.*' => 'exists:drugs,id',
            'satuan' => 'required|array',
            'satuan.*' => 'exists:konversi_satuan,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'harga_jual' => 'required|array',
            'harga_jual.*' => 'integer|min:0',
        ]);

        // Hitung jumlah jenis barang yang ditambahkan
        $totalJenisBarang = count($request->obat_id);

        DB::beginTransaction();

        try {
            // 1️⃣ Simpan Data Penjualan
            $penjualan = Penjualan::create([
                'tanggal_penjualan' => Carbon::now(),
                'total_harga'       => 0,
            ]);

            $totalHarga = 0;

            // 2️⃣ Loop tiap item yang dibeli
            foreach ($request->obat_id as $index => $obatId) {
                $satuanId = $request->satuan[$index];
                $jumlah   = $request->jumlah[$index];
                $harga_jual   = $request->harga_jual[$index];

                // 🔹 Ambil konversi harga berdasarkan obat & satuan
                $konversi = KonversiSatuan::where('obat_id', $obatId)
                    ->where('id', $satuanId)
                    ->first();

                if (!$konversi) {
                    throw new Exception("Satuan tidak ditemukan untuk obat ini.");
                }

                // get Total Jumlah Konversi
                $jumlahSatuanTerkecil = $konversi->jumlah_satuan_terkecil;
                $subtotalJumlah = $jumlah * $jumlahSatuanTerkecil;

                // Ambil stok berdasarkan FIFO (kadaluarsa terdekat), mulai dari etalase
                $stokList = Stok::with('drug')
                    ->where('obat_id', $obatId)
                    // ->where('konversi_satuan_id', $satuanId)
                    ->where(function ($query) {
                        $query->where('stok_etalase', '>', 0)
                            ->orWhere('stok_gudang', '>', 0)
                            ->orWhere('stok_sisa_eceran', '>', 0);
                    })
                    ->whereDate('tanggal_kadaluarsa', '>=', now())
                    ->orderBy('tanggal_kadaluarsa', 'asc')
                    ->get();


                if ($stokList->isEmpty()) {
                    throw new Exception("Stok untuk obat ini sudah kadaluarsa.");
                }

                $sisaJumlah = $subtotalJumlah;
                $subtotal = 0;

                foreach ($stokList as $stok) {
                    if ($sisaJumlah <= 0) break;

                    $stok->stok_sisa_eceran = $stok->stok_sisa_eceran ?? 0;
                    $stok->stok_etalase = $stok->stok_etalase ?? 0;
                    $stok->stok_gudang = $stok->stok_gudang ?? 0;

                    $jumlahTerjual = 0;
                    $ambilDariEtalase = 0;
                    $ambilDariGudang = 0;
                    $ambilDariEceran = 0;
                    $sisaJumlahAwal = $sisaJumlah;

                    // 🔹 Ambil jumlah satuan terkecil (misalnya tablet)
                    $konversiTerkecil = optional($stok->konversiSatuan)->jumlah_satuan_terkecil ?? 1;

                    // 🔹 Jika satuan ini adalah eceran (tablet)
                    if ($jumlahSatuanTerkecil == 1) {
                        // 🔹 Gunakan stok eceran dulu
                        $ambilDariEceran = min($stok->stok_sisa_eceran, $sisaJumlah);
                        $sisaJumlah -= $ambilDariEceran;
                        $stok->stok_sisa_eceran -= $ambilDariEceran;

                        // 🔹 Jika stok eceran tidak cukup, pecah strip dari ETALASE terlebih dahulu
                        if ($sisaJumlah > 0 && $stok->stok_etalase > 0) {
                            $butuhStrip = ceil($sisaJumlah / $konversiTerkecil);
                            $pecahStrip = min($stok->stok_etalase, $butuhStrip);

                            $totalTabletDariStrip = $pecahStrip * $konversiTerkecil;
                            $stok->stok_etalase -= $totalTabletDariStrip;

                            $ambilDariStrip = min($totalTabletDariStrip, $sisaJumlah);
                            $sisaJumlah -= $ambilDariStrip;

                            $stok->stok_sisa_eceran += ($totalTabletDariStrip - $ambilDariStrip);
                        }

                        // 🔹 Jika stok etalase tidak cukup, pecah dari GUDANG
                        if ($sisaJumlah > 0 && $stok->stok_gudang > 0) {
                            $butuhStripGudang = ceil($sisaJumlah / $konversiTerkecil);
                            $pecahStripGudang = min($stok->stok_gudang, $butuhStripGudang);

                            $totalTabletDariStripGudang = $pecahStripGudang * $konversiTerkecil;

                            $stok->stok_gudang -= $totalTabletDariStripGudang;

                            $ambilDariStripGudang = min($totalTabletDariStripGudang, $sisaJumlah);

                            $sisaJumlah -= $ambilDariStripGudang;

                            $stok->stok_sisa_eceran += ($totalTabletDariStripGudang - $ambilDariStripGudang);
                        }
                    } else {
                        // 🔹 Jika bukan eceran, langsung ambil dari stok etalase & gudang
                        $ambilDariEtalase = min($stok->stok_etalase, $sisaJumlah);
                        $sisaJumlah -= $ambilDariEtalase;
                        $stok->stok_etalase -= $ambilDariEtalase;

                        if ($sisaJumlah > 0) {
                            $ambilDariGudang = min($stok->stok_gudang, $sisaJumlah);
                            $sisaJumlah -= $ambilDariGudang;
                            $stok->stok_gudang -= $ambilDariGudang;
                        }
                    }

                    // 🔹 Cek apakah stok masih kurang setelah semua sumber digunakan
                    if ($sisaJumlah > 0) {
                        throw new Exception("Stok {$stok->drug->nama_obat} tidak mencukupi.");
                    }

                    // 🔹 Hitung jumlah total yang terjual
                    $jumlahTerjual = ($sisaJumlahAwal - $sisaJumlah);

                    // Buat Mutasi Stok
                    MutasiStok::create([
                        'stok_id' => $stok->id,
                        'jenis_mutasi' => 'penjualan',
                        'jumlah' => $jumlahTerjual,
                    ]);

                    // 🔹 Simpan perubahan stok
                    $stok->save();
                }

                if ($sisaJumlah > 0) {
                    throw new Exception("Stok obat tidak mencukupi!");
                }

                $subtotal = $jumlah * $harga_jual;
                $totalHarga += $subtotal;

                // 🔹 Simpan Detail Penjualan
                DetailPenjualan::create([
                    'penjualan_id'  => $penjualan->id,
                    'obat_id'       => $obatId,
                    'konversi_satuan_id' => $satuanId,
                    'jumlah'        => $jumlah,
                    'harga_satuan'  => $harga_jual,
                    'subtotal'      => $subtotal,
                ]);
            }

            $penjualan->update(['total_harga' => $totalHarga]);

            // Ambil semua admin dan owner untuk menerima notifikasi
            $admins = User::whereIn('role', ['admin', 'owner'])->get();

            // Buat pesan dan ikon
            $message = "Berhasil melakukan penjualan barang sebanyak {$totalJenisBarang} jenis barang";
            $icon = 'bi bi-cash text-danger';

            // Kirim notifikasi
            Notification::send($admins, new CustomNotification($message, $icon));

            DB::commit();

            return redirect()->route('penjualan')->with('success', 'Penjualan berhasil disimpan!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
