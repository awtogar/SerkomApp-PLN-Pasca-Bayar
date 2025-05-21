<?php
// database/seeders/PelangganSeeder.php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use App\Models\Tarif;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $tarifR1 = Tarif::where('kode_tarif', 'R1/1300VA')->first();
        $tarifR2 = Tarif::where('kode_tarif', 'R2/2200VA')->first();
        $tarifB1 = Tarif::where('kode_tarif', 'B1/2200VA')->first();
        $tarifI1 = Tarif::where('kode_tarif', 'I1/6600VA')->first();

        $pelanggan = [
            [
                'nomor_meter' => '1234567890',
                'nama_pelanggan' => 'Ahmad Wijaya',
                'alamat' => 'Jl. Melati No. 10',
                'id_tarif' => $tarifR1->id,
            ],
            [
                'nomor_meter' => '2345678901',
                'nama_pelanggan' => 'Siti Aminah',
                'alamat' => 'Jl. Mawar No. 5',
                'id_tarif' => $tarifR2->id,
            ],
            [
                'nomor_meter' => '3456789012',
                'nama_pelanggan' => 'Toko Sumber Rezeki',
                'alamat' => 'Jl. Raya Pasar No. 88',
                'id_tarif' => $tarifB1->id,
            ],
            [
                'nomor_meter' => '4567890123',
                'nama_pelanggan' => 'CV Industri Kecil',
                'alamat' => 'Jl. Industri No. 12',
                'id_tarif' => $tarifI1->id,
            ],
        ];

        foreach ($pelanggan as $data) {
            Pelanggan::create($data);
        }
    }
}
