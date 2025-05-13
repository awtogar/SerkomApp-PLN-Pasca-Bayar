<?php
// <!-- /Users/awtogar/Development/tagihan-listrik/app/Models/pelanggan.php -->

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    use HasFactory;
    
    protected $table = 'pelanggan';
    
    protected $fillable = [
        'nomor_meter',
        'nama_pelanggan',
        'alamat',
        'id_tarif',
    ];
    
    public function tarif(): BelongsTo
    {
        return $this->belongsTo(Tarif::class, 'id_tarif');
    }
    
    public function penggunaan(): HasMany
    {
        return $this->hasMany(Penggunaan::class, 'id_pelanggan');
        // return $this->hasMany(Penggunaan::class, 'id_pelanggan')->cascadeOnDelete();
    }
    
    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'id_pelanggan');
        // return $this->hasMany(Tagihan::class, 'id_pelanggan')->cascadeOnDelete();
    }
    
    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_pelanggan');
        // return $this->hasMany(Pembayaran::class, 'id_pelanggan')->cascadeOnDelete();
    }
}