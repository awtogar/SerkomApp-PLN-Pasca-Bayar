<?php
// /Users/awtogar/Development/tagihan-listrik/app/Models/penggunaan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penggunaan extends Model
{
    use HasFactory;
    
    protected $table = 'penggunaan';
    
    protected $fillable = [
        'id_pelanggan',
        'bulan',
        'tahun',
        'meter_awal',
        'meter_akhir',
    ];
    
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
    
    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class, 'id_penggunaan');
        // return $this->hasOne(Tagihan::class, 'id_penggunaan')->cascadeOnDelete();
    }
    
    // Method untuk menghitung jumlah meter
    public function getJumlahMeter(): int
    {
        return $this->meter_akhir - $this->meter_awal;
    }
}
