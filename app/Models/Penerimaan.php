<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemesanan_id',
        'no_faktur',
        'tanggal_penerimaan',
        'penerima',
        'status',
        'catatan',
    ];

    /**
     * Relasi ke tabel Pemesanan (Many to One)
     */
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    /**
     * Relasi ke tabel DetailPenerimaanBarang (One to Many)
     */
    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaan::class);
    }
}
