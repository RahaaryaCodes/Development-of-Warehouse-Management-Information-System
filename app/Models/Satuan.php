<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $guarded = [];

    public function drugs()
    {
        return $this->hasMany(DrugsModel::class);
    }
}
