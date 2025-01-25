<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SuppliersSeeder extends Seeder
{
    public function run()
    {
        // Membuat instance Faker
        $faker = Faker::create();

        // Daftar nama supplier PBF di Indonesia (contoh generik)
        $supplierNames = [
            'Kimia Farma', 'Indofarma', 'PBF Mitra Farma', 'Kalbe Farma', 'Dexa Medica',
            'Bayer Indonesia', 'Merck Indonesia', 'Sido Muncul', 'PT. Pyridam Farma', 'Bio Farma'
        ];

        // Menambahkan data supplier PBF menggunakan Faker
        for ($i = 0; $i < 20; $i++) {
            DB::table('suppliers')->insert([
                'nama_supplier' => $faker->randomElement($supplierNames), // Nama supplier
                'alamat' => $faker->address, // Alamat supplier
                'telepon' => $faker->phoneNumber, // Nomor telepon supplier
                'email' => $faker->email, // Email supplier
                'keterangan' => $faker->sentence, // Keterangan (misalnya: "Supplier obat umum dan khusus")
            ]);
        }
    }
}
