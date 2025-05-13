<?php
// /Users/awtogar/Development/tagihan-listrik/app/Models/petugas.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tagihan extends Model
{
    use HasFactory;
    
    protected $table = 'tagihan';
    
    protected $fillable = [
        'id_penggunaan',
        'id_pelanggan',
        'bulan',
        'tahun',
        'jumlah_meter',
        'status',
        'total_bayar',
    ];
    
    public function penggunaan(): BelongsTo
    {
        return $this->belongsTo(Penggunaan::class, 'id_penggunaan');
    }
    
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
    
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_tagihan');
    }
    

    public function getStatusText(): string
    {
        return $this->status == 1 ? 'Sudah Dibayar' : 'Belum Dibayar';
    }
}