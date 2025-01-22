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
        for ($i = 0; $i < 50; $i++) {
            DB::table('drugs')->insert([
                'batch' => $faker->word, // Contoh batch seperti 'A123'
                'nama_obat' => $faker->word, // Nama obat
                'jenis_obat' => $faker->randomElement(['Obat bebas', 'Obat bebas terbatas', 'Obat keras', 'Obat psikotropika']), // Kategori obat
                'kategori_obat' => $faker->randomElement(['Tablet', 'Kapsul', 'Sirup']), // Jenis obat
                'satuan' => $faker->randomElement(['pcs', 'unit', 'botol', 'strip']),
                'dosis' => $faker->randomElement(['500 mg', '250 mg', '5 mL']), // Dosis obat
                'harga_beli' => $faker->randomFloat(2, 5000, 50000), // Harga beli, contoh: 15000.00
                'harga_jual' => $faker->randomFloat(2, 10000, 100000), // Harga jual, contoh: 25000.00
                'stok' => $faker->numberBetween(10, 1000), // Stok obat
                'stok_minimum' => $faker->numberBetween(5, 100), // Stok minimum
                'tanggal_kadaluarsa' => $faker->date('Y-m-d', '2025-12-31'), // Tanggal kadaluarsa
            ]);
        }
    }
}
