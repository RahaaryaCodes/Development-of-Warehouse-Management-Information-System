<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'satuans';

    protected $fillable = [
        'nama_satuan',
        'konversi',
        'keterangan',
    ];

    public function drugs()
{
    return $this->hasMany(DrugsModel::class, 'satuan_dasar', 'nama_satuan');
}

}
