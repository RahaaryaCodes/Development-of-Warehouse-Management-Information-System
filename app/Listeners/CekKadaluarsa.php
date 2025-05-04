<?php

namespace App\Listeners;

use App\Events\StokUpdated;
use App\Models\User;
use App\Notifications\CustomNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class CekKadaluarsa
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
        $now = Carbon::now();
        $kadaluarsaDate = Carbon::parse($stok->tanggal_kadaluarsa);
        $admins = User::whereIn('role', ['admin', 'owner'])->get();

        // Notifikasi jika stok hampir kadaluarsa (kurang dari 7 hari)
        if ($kadaluarsaDate->lte($now->clone()->addDays(7)) && $kadaluarsaDate->gt($now)) {
            Notification::send($admins, new CustomNotification(
                "Stok obat batch {$stok->batch} akan kadaluarsa pada {$kadaluarsaDate->format('d-m-Y')}.",
                'bi bi-exclamation-circle text-warning'
            ));
        }

        // Notifikasi jika stok sudah kadaluarsa
        if ($kadaluarsaDate->lte($now)) {
            Notification::send($admins, new CustomNotification(
                "Stok obat batch {$stok->batch} telah kadaluarsa pada {$kadaluarsaDate->format('d-m-Y')}.",
                'bi bi-x-circle text-danger'
            ));
        }
    }
}
