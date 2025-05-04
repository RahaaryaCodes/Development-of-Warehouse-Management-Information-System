<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiStok extends Model
{
    protected $table = 'mutasi_stok';

    protected $fillable = [
        'stok_id',
        'jenis_mutasi',
        'jumlah'
    ];

    public function stok()
    {
        return $this->belongsTo(Stok::class, 'stok_id');
    }
}
