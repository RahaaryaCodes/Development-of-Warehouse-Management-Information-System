<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemesanan extends Model
{
    use HasFactory;

    protected $fillable = ['pemesanan_id', 'obat_id', 'jumlah', 'harga_satuan', 'total_harga'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function obat()
    {
        return $this->belongsTo(DrugsModel::class);
    }
}
