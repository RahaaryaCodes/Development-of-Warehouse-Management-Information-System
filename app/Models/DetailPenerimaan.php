<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenerimaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'penerimaan_id',
        'obat_id',
        'no_faktur',
        'jumlah_terima',
        'jumlah_etalase',
        'jumlah_gudang',
        'batch',
        'tanggal_kadaluarsa',
        'tanggal_terima',
        'harga_beli',
        'harga_jual',
        'total_harga',
        'diskon',
        'ppn',
        'satuan',
        'status_detail',
        'zat_aktif',
        'bentuk_sediaan',
        'keterangan',
        'catatan',
    ];

    /**
     * Akses otomatis untuk menghitung total harga setelah diskon & PPN
     */
    public function getTotalHargaAttribute()
    {
        return ($this->harga - $this->diskon) + $this->ppn;
    }

    /**
     * Relasi ke tabel PenerimaanBarang (Many to One)
     */

    /**
     * Relasi ke tabel DrugsModel (Many to One)
     */
    public function obat()
    {
        return $this->belongsTo(DrugsModel::class);
    }
}
