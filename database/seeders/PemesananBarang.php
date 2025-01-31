<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PemesananBarang extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        
        $jenisSurat = [
            'Reguler', 
            'Psikotropika', 
            'OOT', 
            'Prekursor'
        ];
        
        $supplierIds = DB::table('suppliers')->pluck('id')->toArray();

        for ($i = 1; $i <= 50; $i++) {
            $supplierId = $faker->randomElement($supplierIds);

            DB::table('pemesanans')->insert([
                'tanggal_pemesanan' => $faker->date($format = 'Y-m-d', $max = 'now'),
                'supplier_id' => $supplierId,
                'jenis_surat' => $faker->randomElement($jenisSurat),  // Pastikan memilih nilai yang sesuai dengan enum
                'total_harga' => $faker->randomNumber(7),
                'status' => $faker->randomElement(['Menunggu Konfirmasi', 'Dikirim', 'Selesai']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
