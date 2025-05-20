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
    'golongan',     //Contoh: R1, R2, R3, B1, B2, I1, I2
    'deskripsi',    //Contoh: Unit bisnis umkm, unit bisnis industri, unit bisnis rumah tangga
    'daya',         //Contoh: 450 VA, 900 VA, 1300 VA
    'tarif_perkwh', //Contoh: 1000, 2000, 3000
];

protected static function booted()
{
    static::creating(function ($tarif) {
        $dayaVoltAmpere = str_replace(' ', '', strtoupper($tarif->daya));
        $tarif->kode_tarif = $tarif->golongan . '/' . $dayaVoltAmpere;
    });
}


    
    public function pelanggan(): HasMany
    {
        return $this->hasMany(Pelanggan::class, 'id_tarif');
    }
}