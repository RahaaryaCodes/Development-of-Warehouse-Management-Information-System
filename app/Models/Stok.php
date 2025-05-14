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

    // === Relasi ===
   // Di file Stok.php, tambahkan alias relasi
public function drug()
{
    return $this->belongsTo(DrugsModel::class, 'obat_id');
}


    public function konversiSatuan()
    {
        return $this->belongsTo(KonversiSatuan::class, 'konversi_satuan_id');
    }

    // === Total stok helper ===
    public function getTotalStokAttribute()
    {
        return $this->stok_gudang + $this->stok_etalase;
    }

    // === Event listener saat stok diupdate ===
    protected static function booted()
    {
        static::updated(function ($stok) {
            event(new StokUpdated($stok));
        });
    }
}
