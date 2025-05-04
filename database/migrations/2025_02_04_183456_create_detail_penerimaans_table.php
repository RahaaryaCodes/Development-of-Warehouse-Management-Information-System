<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('detail_penerimaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_id')->constrained('pemesanans')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('drugs')->onDelete('cascade');
            $table->string('no_faktur')->unique();
            $table->integer('jumlah_terima');
            $table->integer('jumlah_etalase');
            $table->integer('jumlah_gudang');
            $table->string('batch');
            $table->date('tanggal_kadaluarsa');
            $table->date('tanggal_terima');
            $table->integer('harga_beli');
            $table->integer('harga_jual');
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->integer('total_harga')->default(0);
            $table->string('satuan');
            $table->enum('status_detail', ['Baik', 'Rusak'])->default('Baik');
            $table->string('zat_aktif')->nullable();
            $table->string('bentuk_sediaan')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_penerimaans');
    }
};
