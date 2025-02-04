<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'tanggal_pemesanan', 'jenis_surat', 'status'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function detailPemesanan()
    {
        return $this->hasMany(DetailPemesanan::class, 'pemesanan_id');
    }
    public function penerimaanBarang()
{
    return $this->hasOne(Pemesanan::class, 'pemesanan_id');
}

}

