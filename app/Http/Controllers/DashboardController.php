<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use App\Models\DrugsModel; 
use Carbon\Carbon; 
use App\Models\Penjualan; 
use App\Models\Pemesanan;
use App\Models\MutasiStok;
use App\Models\Stok; 
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller 
{
    public function index(Request $request) 
    {
        $rentangWaktu = $request->input('rentang', 'bulan_ini');
        
        $dashboardData = $this->getDashboardData($rentangWaktu);
        
        return view('dashboard', $dashboardData);
    }

    public function getTransaksiChart(Request $request)
    {
        $rentangWaktu = $request->input('rentang', 'bulan_ini');
        
        // Tentukan rentang tanggal
        switch ($rentangWaktu) {
            case 'minggu_ini':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $format = 'D'; // Format hari (Mon, Tue, etc)
                break;
            
            case 'bulan_ini':
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $format = 'd'; // Format tanggal (1, 2, 3, etc)
                break;
        }

        // Query untuk Penjualan
        $penjualan = Penjualan::select(
            DB::raw('DATE(tanggal_penjualan) as date'),
            DB::raw('COUNT(*) as total')
        )
        ->whereBetween('tanggal_penjualan', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        // Query untuk Pemesanan
        $pemesanan = Pemesanan::select(
            DB::raw('DATE(tanggal_pemesanan) as date'),
            DB::raw('COUNT(*) as total')
        )
        ->whereBetween('tanggal_pemesanan', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        // Siapkan data untuk chart
        $chartData = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $chartData[] = [
                'name' => $currentDate->format($format),
                'Penjualan' => $penjualan->has($dateKey) ? $penjualan[$dateKey]['total'] : 0,
                'Pemesanan' => $pemesanan->has($dateKey) ? $pemesanan[$dateKey]['total'] : 0
            ];
            $currentDate->addDay();
        }

        return response()->json($chartData);
    }

    public function getDashboardData($rentangWaktu = 'bulan_ini') 
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $totalObat = DrugsModel::count();
        
        $obatHampirHabis = DrugsModel::whereHas('stok', function ($q) {
            $q->whereColumn('stok_gudang', '<', 'stock_minimum');
        })->count();
        
        $obatKedaluwarsa = Stok::whereDate('tanggal_kadaluarsa', '<', now())
            ->distinct('obat_id')
            ->count();
        
        // Logika dinamis untuk rentang waktu
        switch ($rentangWaktu) {
            case 'minggu_ini':
                $penjualanQuery = Penjualan::whereBetween('tanggal_penjualan', [
                    Carbon::now()->startOfWeek(), 
                    Carbon::now()->endOfWeek()
                ]);
                $pemesananQuery = Pemesanan::whereBetween('tanggal_pemesanan', [
                    Carbon::now()->startOfWeek(), 
                    Carbon::now()->endOfWeek()
                ]);
                break;
            
            case 'bulan_ini':
            default:
                $penjualanQuery = Penjualan::whereBetween('tanggal_penjualan', [
                    Carbon::now()->startOfMonth(), 
                    Carbon::now()->endOfMonth()
                ]);
                $pemesananQuery = Pemesanan::whereBetween('tanggal_pemesanan', [
                    Carbon::now()->startOfMonth(), 
                    Carbon::now()->endOfMonth()
                ]);
        }
        
        // Hitung total transaksi
        $transaksiHariIni = $penjualanQuery->count() + $pemesananQuery->count();
        
        return [
            'totalObat' => $totalObat,
            'obatHampirHabis' => $obatHampirHabis,
            'obatKedaluwarsa' => $obatKedaluwarsa,
            'transaksiHariIni' => $transaksiHariIni,
            'rentangWaktu' => $rentangWaktu
        ];
    }
}