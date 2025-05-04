<?php

namespace App\Providers;

use App\Events\StokUpdated;
use App\Listeners\CekKadaluarsa;
use App\Listeners\CekStokMenipis;
use App\Models\Stok;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('role', \App\Http\Middleware\CheckRole::class);

        // Listeners untuk cek kadaluarsa dan stok menipis
        Event::listen(Login::class, function ($event) {
            $stokItems = Stok::with('drug')->get();

            foreach ($stokItems as $stok) {
                event(new StokUpdated($stok));
            }
        });
    }
}
