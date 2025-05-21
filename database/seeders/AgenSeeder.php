<?php

namespace Database\Seeders;

use App\Models\Agen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgenSeeder extends Seeder
{
    public function run(): void
    {
        $agens = [
            [
                'username'     => 'agen1',
                'email'        => 'agen1@example.com',
                'password'     => Hash::make('password123'),
                'nama_agen'    => 'Agen Pertama',
                'alamat_agen'  => 'Jl. Melati No.1, Depok',
                'no_telepon'   => '081234567890',
            ],
            [
                'username'     => 'agen2',
                'email'        => 'agen2@example.com',
                'password'     => Hash::make('password123'),
                'nama_agen'    => 'Agen Kedua',
                'alamat_agen'  => 'Jl. Mawar No.2, Jakarta',
                'no_telepon'   => '081298765432',
            ],
        ];

        foreach ($agens as $agen) {
            Agen::create($agen);
        }
    }
}
