<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drugs', function (Blueprint $table) {
            $table->id(); // Kolom ID
            $table->string('batch')->unique(); // Kolom Batch
            $table->string('nama_obat'); // Nama Obat
            $table->string('kategori_obat'); // Kategori Obat
            $table->string('jenis_obat'); // Jenis Obat
            $table->string('satuan'); // Satuan Obat
            $table->string('dosis'); // Dosis Obat
            $table->decimal('harga_beli', 10, 2); // Harga Beli
            $table->decimal('harga_jual', 10, 2); // Harga Jual
            $table->integer('stok'); // Jumlah Stok
            $table->integer('stok_minimum'); // Stok Minimum
            $table->date('tanggal_kadaluarsa'); // Tanggal Kadaluarsa
            $table->timestamps(); // Menambahkan created_at dan updated_at secara otomatis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drugs');
    }
};
