<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tarif', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tarif')->unique();
            $table->enum('golongan', ['R1', 'R2', 'R3', 'B1', 'B2', 'B3', 'I1', 'I2','I3','I4','P1','P2','P3']) ->default('R1');
            $table->string('deskripsi');
            $table->string('daya');
            $table->decimal('tarif_perkwh', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tarif');
    }
};