<?php

namespace App\Imports;

use App\Models\DrugsModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ObatImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new DrugsModel([
            'nama_obat'      => $row['nama_obat'],
            'kategori_obat'  => $row['kategori_obat'],
            'jenis_obat'     => $row['jenis_obat'],
            'golongan_obat'  => $row['golongan_obat'],
            'satuan_dasar'   => $row['satuan_dasar'],
            'stock_minimum'  => $row['stock_minimum'],
        ]);
    }
}
