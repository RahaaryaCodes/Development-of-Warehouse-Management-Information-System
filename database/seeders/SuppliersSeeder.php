<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SuppliersSeeder extends Seeder
{
    public function run()
    {
        DB::table('suppliers')->insert([
            [
                'id' => 1,
                'nama_supplier' => 'PT Kimia Farma',
                'alamat' => 'Yogyakarta',
                'telepon' => '0822382184112',
                'email' => 'kimia@mail.com',
                'keterangan' => 'Supplier kimia farma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nama_supplier' => 'PT Biologi Farma',
                'alamat' => 'Bandung',
                'telepon' => '0823834855663',
                'email' => 'biologi@mail.com',
                'keterangan' => 'Supplier Biologi Farma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
