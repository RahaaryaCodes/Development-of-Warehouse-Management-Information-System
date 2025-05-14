<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugsModel extends Model
{
    use HasFactory;

    protected $table = 'drugs';

    protected $fillable = [
        'nama_obat',
        'kategori_obat',
        'jenis_obat',
        'golongan_obat',
        'satuan_dasar',
        'stock_minimum'
    ];

    public $timestamps = true;

    // === Relasi ===
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class, 'nama_golongan');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_dasar', 'nama_satuan');
    }

    public function konversiSatuan()
    {
        return $this->hasMany(KonversiSatuan::class, 'obat_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function penerimaanBarang()
    {
        return $this->hasOne(Penerimaan::class, 'pemesanan_id');
    }

    // Relasi utama ke stok satuan
    public function stok()
    {
        return $this->hasOne(Stok::class, 'obat_id');
    }

    // Jika satu obat bisa punya banyak stok (opsional)
    public function stokList()
    {
        return $this->hasMany(Stok::class, 'obat_id');
    }

    // === Akses total stok dari relasi stok ===
    public function getTotalStokAttribute()
    {
        return optional($this->stok)->stok_gudang + optional($this->stok)->stok_etalase;
    }
}
