<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'satuans';

    protected $fillable = [
        'nama_satuan',
        'keterangan',
    ];

    // App\Models\Satuan.php
    public function drugs()
    {
        return $this->hasMany(DrugsModel::class, 'satuan', 'nama_satuan');
    }


}
