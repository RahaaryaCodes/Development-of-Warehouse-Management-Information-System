<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemesanan extends Model
{
    use HasFactory;

    protected $fillable = ['pemesanan_id', 'obat_id', 'jumlah', 'satuan', 'zat_aktif', 'bentuk_sediaan', 'keterangan'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }

    public function obat()
    {
        return $this->belongsTo(DrugsModel::class, 'obat_id');
    }
    

}
