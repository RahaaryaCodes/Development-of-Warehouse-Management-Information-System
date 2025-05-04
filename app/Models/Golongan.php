<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    use HasFactory;
    protected $fillable = ['nama_golongan', 'keterangan'];

    public function drugs()
    {
        return $this->hasMany(DrugsModel::class);
    }
}
