<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penerimaan;
use App\Models\Supplier;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PenerimaanSeeder extends Seeder
{
    public function run()
{
    $faker = Faker::create();
    
    // Ambil supplier dan pemesanan yang sudah ada
    $suppliers = Supplier::pluck('id')->toArray();
    $pemesanans = Pemesanan::pluck('id')->toArray();

    for ($i = 0; $i < 10; $i++) {
        Penerimaan::create([
            'no_faktur' => strtoupper($faker->bothify('FAK-#####')),
            'tanggal_penerimaan' => $faker->date(),
            'supplier_id' => $faker->randomElement($suppliers),
            'pemesanan_id' => $faker->randomElement($pemesanans),
            'jenis_surat' => $faker->randomElement(['Reguler', 'Psikotropika', 'OOT', 'Prekursor']),
            'status' => $faker->randomElement(['Menunggu Konfirmasi', 'Diterima', 'Ditolak']),  // Pilih status yang valid
        ]);
    }
}

}
