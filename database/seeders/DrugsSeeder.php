<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DrugsSeeder extends Seeder
{
    public function run()
    {
        DB::table('drugs')->insert([
            [
                'id' => 1,
                'nama_obat' => 'Ambroxol',
                'kategori_obat' => 'Obat Bebas',
                'jenis_obat' => 'Obat Sakit',
                'satuan_dasar' => 'tablet',
                'stock_minimum' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nama_obat' => 'Sirup',
                'kategori_obat' => 'Obat Bebas',
                'jenis_obat' => 'Obat Vitamin',
                'satuan_dasar' => 'botol',
                'stock_minimum' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nama_obat' => 'Paramadol',
                'kategori_obat' => 'Obat Keras',
                'jenis_obat' => 'Obat berbahaya',
                'satuan_dasar' => 'tablet',
                'stock_minimum' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
