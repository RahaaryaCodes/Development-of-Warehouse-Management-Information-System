<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DrugsController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'postLogin']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    
    // Gunakan Route resource untuk CRUD data obat
    Route::get('/data-obat/search', [DrugsController::class, 'search'])->name('data-obat.search');
    Route::resource('data-obat', DrugsController::class);
    
    Route::get('/data-supplier/search', [SupplierController::class, 'search'])->name('data-supplier.search');
    Route::resource('data-supplier', SupplierController::class);
    
    Route::get('/satuan/search', [SatuanController::class, 'search'])->name('data-satuan.search');
    Route::resource('data-satuan', SatuanController::class);
    
    
    Route::resource('data-kategori', KategoriController::class);
    Route::get('/kategori/search', [KategoriController::class, 'search'])->name('kategori.search');
    
    Route::resource('pemesanan-barang', PemesananController::class);
    Route::get('/pemesanan/search', [PemesananController::class, 'search'])->name('pemesanan.search');

    Route::resource('penerimaan-barang', PenerimaanController::class);
    Route::get('/penerimaan/search', [PenerimaanController::class, 'search'])->name('penerimaan.search');
});



    