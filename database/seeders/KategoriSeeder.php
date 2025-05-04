<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('kategoris')->insert([
            ['id' => 1, 'nama_kategori' => 'Obat Bebas', 'keterangan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_kategori' => 'Obat Bebas Terbatas', 'keterangan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_kategori' => 'Obat Keras', 'keterangan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_kategori' => 'Obat Psikotropika', 'keterangan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama_kategori' => 'Obat Narkotika', 'keterangan' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
