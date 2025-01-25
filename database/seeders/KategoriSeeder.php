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
        // Menggunakan Faker untuk generate data
        $faker = Faker::create();

        // Menambahkan kategori obat menggunakan Faker
        for ($i = 0; $i < 20; $i++) {  // Membuat 10 kategori
            DB::table('kategoris')->insert([
                'nama_kategori' => $faker->word, // Faker akan generate kata acak untuk kategori
                'keterangan' => $faker->sentence
            ]);
        }
    }
}
