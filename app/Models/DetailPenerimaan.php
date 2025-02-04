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
        'batch',
        'tanggal_kadaluarsa',
        'harga',
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
    public function penerimaanBarang()
    {
        return $this->belongsTo(Penerimaan::class);
    }

    /**
     * Relasi ke tabel DrugsModel (Many to One)
     */
    public function obat()
    {
        return $this->belongsTo(DrugsModel::class);
    }
    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'penerimaan_id');
    }

}
