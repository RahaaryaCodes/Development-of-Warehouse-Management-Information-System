<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('penerimaans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_penerimaan');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('pemesanan_id')->nullable()->constrained('pemesanans')->onDelete('set null');
            $table->string('jenis_surat');
            $table->enum('status', ['Menunggu Konfirmasi', 'Diterima', 'Ditolak'])->default('Menunggu Konfirmasi');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaans');
    }
};
