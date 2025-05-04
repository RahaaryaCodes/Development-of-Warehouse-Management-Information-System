<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('drugs')->onDelete('cascade');
            $table->foreignId('konversi_satuan_id')->constrained('konversi_satuan')->onDelete('cascade');
            $table->string('batch')->unique();
            $table->integer('stok_gudang')->default(0);
            $table->integer('stok_etalase')->default(0);
            $table->integer('stok_sisa_eceran')->default(0);
            $table->integer('stok_apotik_cabang')->default(0);
            $table->integer('harga_beli');
            $table->integer('harga_jual');
            $table->date('tanggal_kadaluarsa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};
