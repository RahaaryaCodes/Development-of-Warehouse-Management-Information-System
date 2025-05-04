<?php

namespace App\Listeners;

use App\Events\StokUpdated;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class CekStokMenipis
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StokUpdated $event)
    {
        $stok = $event->stok;
        $konversiSatuan = $stok->konversiSatuan->jumlah_satuan_terkecil;

        // Konversi stok etalase dan gudang ke satuan dasarnya
        $hasilKonversiEtalase = $stok->stok_etalase / $konversiSatuan;
        $hasilKonversiGudang = $stok->stok_gudang / $konversiSatuan;

        $admins = User::whereIn('role', ['admin', 'owner'])->get();

        // Cek stok etalase
        if ($stok->stok_etalase == 0) {
            // Notifikasi stok etalase habis
            Notification::send($admins, new CustomNotification(
                "Stok etalase telah habis: {$stok->drug->nama_obat} ({$stok->batch})",
                'bi bi-exclamation-circle text-danger'
            ));
        } elseif ($stok->stok_etalase <= $stok->drug->stock_minimum) {
            // Notifikasi stok etalase menipis
            Notification::send($admins, new CustomNotification(
                "Stok etalase telah menipis: {$stok->drug->nama_obat} ({$stok->batch}), sisa: {$hasilKonversiEtalase} {$stok->drug->satuan_dasar}",
                'bi bi-cash text-warning'
            ));
        }

        // Cek stok gudang
        if ($stok->stok_gudang == 0) {
            // Notifikasi stok gudang habis
            Notification::send($admins, new CustomNotification(
                "Stok gudang telah habis: {$stok->drug->nama_obat} ({$stok->batch})",
                'bi bi-exclamation-circle text-danger'
            ));
        } elseif ($stok->stok_gudang <= $stok->drug->stock_minimum) {
            // Notifikasi stok gudang menipis
            Notification::send($admins, new CustomNotification(
                "Stok gudang telah menipis: {$stok->drug->nama_obat} ({$stok->batch}), sisa: {$hasilKonversiGudang} {$stok->drug->satuan_dasar}",
                'bi bi-cash text-danger'
            ));
        }
    }
}
