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
        Schema::create('konversi_satuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('drugs')->onDelete('cascade');
            $table->string('nama_satuan');
            $table->integer('jumlah_satuan_terkecil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konversi_satuan');
    }
};
