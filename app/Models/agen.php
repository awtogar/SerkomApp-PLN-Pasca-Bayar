<?php
// /Users/awtogar/Development/tagihan-listrik/app/Models/agen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Agen extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'agen';
    
    protected $fillable = [
        'username',
        'password',
        'nama_agen',
        'alamat_agen',
        'no_telepon',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_agen');
        // return $this->hasMany(Pembayaran::class, 'id_agen')->cascadeOnDelete();
    }
}