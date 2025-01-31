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
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->date('tanggal_pemesanan');
            $table->enum('jenis_surat', ['Reguler', 'Psikotropika', 'OOT', 'Prekursor']);
            $table->decimal('total_harga', 15, 2);
            $table->enum('status', ['Menunggu Konfirmasi', 'Dikirim', 'Selesai'])->defaulat('Menunggu Konfirmasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
