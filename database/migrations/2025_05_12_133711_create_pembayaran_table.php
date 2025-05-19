<?php
// /Users/awtogar/Development/tagihan-listrik/database/migrations/2025_05_12_133711_create_pembayarans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tagihan')->constrained('tagihan');
            $table->foreignId('id_pelanggan')->constrained('pelanggan');
            $table->date('tanggal_pembayaran');
            $table->string('bulan_bayar');
            $table->integer('tahun_bayar');
            $table->decimal('biaya_admin', 10, 2);
            $table->decimal('total_bayar', 10, 2);
            $table->foreignId('id_agen')->constrained('agen');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
};
