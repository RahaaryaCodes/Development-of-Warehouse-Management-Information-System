<?php

namespace App\Models;

use App\Events\StokUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stok';

    protected $fillable = [
        'obat_id',
        'konversi_satuan_id',
        'batch',
        'stok_gudang',
        'stok_etalase',
        'stok_sisa_eceran',
        'stok_apotik_cabang',
        'harga_beli',
        'harga_jual',
        'tanggal_kadaluarsa',
    ];

    // Relasi ke model Drug
    public function drug()
    {
        return $this->belongsTo(DrugsModel::class, 'obat_id');
    }

    // Menghitung total stok
    public function getTotalStokAttribute()
    {
        return $this->stok_gudang + $this->stok_etalase;
    }

    // Relasi ke model Konversi Satuan
    public function konversiSatuan()
    {
        return $this->belongsTo(KonversiSatuan::class, 'konversi_satuan_id');
    }

    protected static function booted()
    {
        static::updated(function ($stok) {
            event(new StokUpdated($stok));
        });
    }
}
