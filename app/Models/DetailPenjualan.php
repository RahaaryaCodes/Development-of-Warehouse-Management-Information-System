<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualans';

    protected $fillable = [
        'penjualan_id',
        'obat_id',
        'konversi_satuan_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    public function obat()
    {
        return $this->belongsTo(DrugsModel::class);
    }

    public function konversiSatuan()
    {
        return $this->belongsTo(KonversiSatuan::class);
    }
}
