<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DrugsSeeder extends Seeder
{
    public function run()
    {
        // Membuat instance Faker
        $faker = Faker::create();

        // Menambahkan data obat menggunakan Faker
        for ($i = 0; $i < 30; $i++) {
            DB::table('drugs')->insert([
                'batch' => strtoupper($faker->bothify('???###')), // Batch seperti 'A123'
                'nama_obat' => $faker->word, 
                'kategori_obat' => $faker->randomElement(['Obat bebas', 'Obat bebas terbatas', 'Obat keras', 'Obat psikotropika']), // Kategori obat
                'jenis_obat' => $faker->randomElement(['Tablet', 'Kapsul', 'Sirup']), // Jenis obat
                'satuan' => $faker->randomElement(['pcs', 'unit', 'botol', 'strip']),
                'harga_beli' => $faker->numberBetween(5000, 50000), // Harga beli tanpa desimal
                'harga_jual' => $faker->numberBetween(10000, 100000), // Harga jual tanpa desimal
                'stok' => $faker->numberBetween(10, 1000), // Stok obat
                'stok_minimum' => $faker->numberBetween(5, 100), // Stok minimum
                'tanggal_kadaluarsa' => $faker->date('Y-m-d', '2025-12-31'), // Tanggal kadaluarsa
            ]);
        }
    }
}
