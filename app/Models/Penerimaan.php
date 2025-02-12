<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    use HasFactory;
    protected $fillable = ['tanggal_penerimaan', 'supplier_id', 'pemesanan_id', 'no_faktur', 'jenis_surat', 'status'];

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaan::class, 'penerimaan_id');
    }
    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Relasi ke PemesananBarang
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }
}
