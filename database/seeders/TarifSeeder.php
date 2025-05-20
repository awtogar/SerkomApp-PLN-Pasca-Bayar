<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarif;

class TarifSeeder extends Seeder
{
    public function run(): void
    {
        $tarifs = [
            [
                'kode_tarif' => 'R1',
                'golongan_tarif' => 'Rumah Tangga',
                'daya' => 1300,
                'tarif_perkwh' => 1467.28,
            ],
            [
                'kode_tarif' => 'R2',
                'golongan_tarif' => 'Rumah Tangga Menengah',
                'daya' => 2200,
                'tarif_perkwh' => 1467.28,
            ],
            [
                'kode_tarif' => 'B1',
                'golongan_tarif' => 'Bisnis Kecil',
                'daya' => 2200,
                'tarif_perkwh' => 1444.70,
            ],
            [
                'kode_tarif' => 'I1',
                'golongan_tarif' => 'Industri Kecil',
                'daya' => 6600,
                'tarif_perkwh' => 1115.00,
            ],
        ];

        foreach ($tarifs as $tarif) {
            Tarif::create($tarif);
        }
    }
}
