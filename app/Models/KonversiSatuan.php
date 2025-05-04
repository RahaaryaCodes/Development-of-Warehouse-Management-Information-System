<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonversiSatuan extends Model
{
    protected $table = 'konversi_satuan';

    protected $fillable = [
        'obat_id',
        'nama_satuan',
        'jumlah_satuan_terkecil',
    ];

    public function drug()
    {
        return $this->belongsTo(DrugsModel::class, 'obat_id');
    }
}
