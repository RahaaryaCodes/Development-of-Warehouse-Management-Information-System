<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugsModel extends Model
{
    protected $table = 'drugs';
    protected $fillable = [
        'batch',
        'nama_obat',
        'kategori_obat',
        'jenis_obat',
        'satuan',
        'dosis',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'tanggal_kadaluarsa',
    ];
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public $timestamps = true;

    public function getTanggalKadaluarsaAttribute($value)
    {
        return Carbon::parse($value); // Mengonversi string menjadi objek Carbon
        
    }
}
