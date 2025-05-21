<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarif;

class TarifSeeder extends Seeder
{
    // database/seeders/TarifSeeder.php

public function run(): void
{
    $tarifs = [
        [
            'kode_tarif' => 'R1/1300VA',
            'golongan' => 'R1',
            'deskripsi' => 'Rumah Tangga (1300 VA)',
            'daya' => 1300,
            'tarif_perkwh' => 1467.28,
        ],
        [
            'kode_tarif' => 'R2/2200VA',
            'golongan' => 'R2',
            'deskripsi' => 'Rumah Tangga Menengah (2200 VA)',
            'daya' => 2200,
            'tarif_perkwh' => 1467.28,
        ],
        [
            'kode_tarif' => 'B1/2200VA',
            'golongan' => 'B1',
            'deskripsi' => 'Bisnis Kecil (2200 VA)',
            'daya' => 2200,
            'tarif_perkwh' => 1444.70,
        ],
        [
            'kode_tarif' => 'I1/6600VA',
            'golongan' => 'I1',
            'deskripsi' => 'Industri Kecil (6600 VA)',
            'daya' => 6600,
            'tarif_perkwh' => 1115.00,
        ],
    ];

    foreach ($tarifs as $tarif) {
        Tarif::create($tarif);
    }
}
}