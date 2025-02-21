<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemesanan extends Model
{
    use HasFactory;

    protected $fillable = ['pemesanan_id', 'obats', 'catatan'];

    protected $casts = [
        'obats' => 'array', // Mengonversi 'obats' menjadi array
    ];
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }
    
}
