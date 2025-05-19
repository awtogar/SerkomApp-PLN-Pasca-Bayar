<?php
// /Users/awtogar/Development/tagihan-listrik/app/Models/petugas.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Petugas extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'petugas';
    
    protected $fillable = [
        'username',
        'email',
        'password',
        'nama_petugas',
        'level',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
