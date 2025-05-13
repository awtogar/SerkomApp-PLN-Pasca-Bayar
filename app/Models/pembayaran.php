<?php
// /Users/awtogar/Development/tagihan-listrik/app/Models/pembayaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;
    
    protected $table = 'pembayaran';
    
    protected $fillable = [
        'id_tagihan',
        'id_pelanggan',
        'tanggal_pembayaran',
        'bulan_bayar',
        'tahun_bayar',
        'biaya_admin',
        'total_bayar',
        'id_agen',
    ];
    
    protected $casts = [
        'tanggal_pembayaran' => 'date',
    ];
    
    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan');
    }
    
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
    
    public function agen(): BelongsTo
    {
        return $this->belongsTo(Agen::class, 'id_agen');
    }
}