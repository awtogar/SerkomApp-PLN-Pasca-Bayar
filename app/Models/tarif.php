<?php
// <!-- /Users/awtogar/Development/tagihan-listrik/app/Models/petugas.php -->

// app/Models/Tarif.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    use HasFactory;
    
    protected $table = 'tarif';
    
    protected $fillable = [
        'kode_tarif',
        'golongan_tarif',
        'daya',
        'tarif_perkwh',
    ];
    
    public function pelanggan(): HasMany
    {
        return $this->hasMany(Pelanggan::class, 'id_tarif');
    }
}