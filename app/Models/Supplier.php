<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public function drugs()
    {
        return $this->hasMany(DrugsModel::class);
    }
    public function penerimaanBarang()
{
    return $this->hasMany(Penerimaan::class, 'supplier_id');
}


}
