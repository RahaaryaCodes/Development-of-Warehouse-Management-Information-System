<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('satuans')->insert([
            ['id' => 1, 'nama_satuan' => 'Botol', 'konversi' => 1, 'keterangan' => '1 botol', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_satuan' => 'Tablet', 'konversi' => 1, 'keterangan' => '1 tablet', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_satuan' => 'Strip (6 tablet)', 'konversi' => 6, 'keterangan' => '6 tablet', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_satuan' => 'Strip (10 tablet)', 'konversi' => 10, 'keterangan' => '10 tablet', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama_satuan' => 'box (5 strip)', 'konversi' => 30, 'keterangan' => 'Strip 6 tablet', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
