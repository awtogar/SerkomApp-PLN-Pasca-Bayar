<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pln@pascabayar.test'],
            [
                'name' => 'Admin PLN',
                'password' => Hash::make('password'),
            ]
        );
    }
}
