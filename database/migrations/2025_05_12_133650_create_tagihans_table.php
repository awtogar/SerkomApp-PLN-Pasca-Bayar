<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penggunaan')->constrained('penggunaan');
            $table->foreignId('id_pelanggan')->constrained('pelanggan');
            $table->string('bulan');
            $table->integer('tahun');
            $table->integer('jumlah_meter');
            $table->decimal('status', 1, 0)->default(0); // 0 = belum bayar, 1 = sudah bayar
            $table->decimal('total_bayar', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tagihan');
    }
};