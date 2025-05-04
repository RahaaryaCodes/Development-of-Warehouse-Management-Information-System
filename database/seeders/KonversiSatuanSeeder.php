<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KonversiSatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('konversi_satuan')->insert([
            ['id' => 1, 'obat_id' => 1, 'nama_satuan' => 'Tablet', 'jumlah_satuan_terkecil' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'obat_id' => 1, 'nama_satuan' => 'Strip (6 tablet)', 'jumlah_satuan_terkecil' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'obat_id' => 1, 'nama_satuan' => 'box (5 strip)', 'jumlah_satuan_terkecil' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'obat_id' => 2, 'nama_satuan' => 'Botol', 'jumlah_satuan_terkecil' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'obat_id' => 3, 'nama_satuan' => 'Tablet', 'jumlah_satuan_terkecil' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'obat_id' => 3, 'nama_satuan' => 'Strip (6 tablet)', 'jumlah_satuan_terkecil' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
