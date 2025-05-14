<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DrugsController;
use App\Http\Controllers\GolonganController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KonversiSatuanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/dashboard/chart', [DashboardController::class, 'getTransaksiChart'])->name('dashboard.chart');


    // Gunakan Route resource untuk CRUD data obat
    Route::get('/data-obat/search', [DrugsController::class, 'search'])->name('data-obat.search');
    Route::resource('data-obat', DrugsController::class);
    Route::get('/download-template', [DrugsController::class, 'downloadTemplate'])->name('download.template');
    Route::post('/import-obat', [DrugsController::class, 'importExcel'])->name('import.excel');
    Route::get('/export-excel', [DrugsController::class, 'export'])->name('export.excel');

    Route::get('/data-supplier/search', [SupplierController::class, 'search'])->name('data-supplier.search');
    Route::resource('data-supplier', SupplierController::class);

    Route::get('/satuan/search', [SatuanController::class, 'search'])->name('data-satuan.search');
    Route::resource('data-satuan', SatuanController::class);
    Route::get('/data-satuan/list', [SatuanController::class, 'list'])->name('data-satuan.list');

    Route::resource('konversi-satuan', KonversiSatuanController::class);

    Route::resource('data-kategori', KategoriController::class);
    Route::get('/kategori/search', [KategoriController::class, 'search'])->name('kategori.search');

   
    Route::resource('data-golongan', GolonganController::class);
    Route::get('/golongan/search', [GolonganController::class, 'search'])->name('golongan.search');


    Route::resource('pemesanan-barang', PemesananController::class);
    Route::get('/pemesanan-barang/{id}/cetak-pdf', [PemesananController::class, 'cetakPdf'])->name('pemesanan-barang.cetak-pdf');
    Route::post('/pemesanan-barang/{id}/update-status', [PemesananController::class, 'updateStatus'])->name('pemesanan.update-status');
    Route::get('/pemesanan/search', [PemesananController::class, 'search'])->name('pemesanan.search');

    Route::post('/detail-penerimaan/store', [PenerimaanController::class, 'storeDetailPenerimaan'])->name('detail-penerimaan.store');

    // Tambahkan route untuk pencarian obat agar tidak error
    Route::get('/search/obat', [DrugsController::class, 'search'])->name('search.obat');
    Route::get('/penerimaan/search', [PenerimaanController::class, 'search'])->name('penerimaan.search');

    // Stok Gudang
    Route::get('/stok-gudang', [StokController::class, 'stokGudang'])->name('stok-gudang');
    Route::get('/stok-etalase', [StokController::class, 'stokEtalase'])->name('stok-etalase');
    Route::get('/pemindahan-stok', [StokController::class, 'pemindahanStok'])->name('pemindahan-stok');
    Route::post('/pemindahan-stok/update', [StokController::class, 'updateStok'])->name('pemindahan-stok.update');

    // Penjualan
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan');
    Route::post('/penjualan/jual', [PenjualanController::class, 'store'])->name('penjualan.store');

    // Laporan
    Route::get('/laporan-penjualan', [LaporanController::class, 'laporanPenjualan'])->name('laporan-penjualan');
    Route::get('/detail-penjualan/{id}', [LaporanController::class, 'detailPenjualan'])->name('detail-penjualan');
    Route::get('/laporan-stok', [LaporanController::class, 'laporanStok'])->name('laporan-stok');
    Route::get('/laporan-kadaluarsa', [LaporanController::class, 'laporanObatKadaluarsa'])->name('laporan-obat-kadaluarsa');

    // API
    Route::get('/get-satuan/{obat_id}', [ApiController::class, 'getSatuanByObat']);
    Route::get('/batches/{obat_id}', [ApiController::class, 'getBatches']);
    Route::get('/pemesanan-barang/{id}/get-obat', [ApiController::class, 'getObatPemesanan']);

    // Notifications
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/count', function () {
        return response()->json([
            'count' => auth()->user()->unreadNotifications->count()
        ]);
    });
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');



});
