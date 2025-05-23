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
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'id_pelanggan');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_pelanggan');
    }

    // Method untuk mendapatkan penggunaan terakhir
    public function getPenggunaanTerakhir(): ?Penggunaan
    {
        return $this->penggunaan()
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();
    }

    // Method untuk mendapatkan tagihan yang belum dibayar
    public function getTagihanBelumDibayar(): HasMany
    {
        return $this->tagihan()->belumDibayar();
    }

    // Method untuk mendapatkan total tunggakan
    public function getTotalTunggakan(): float
    {
        return $this->tagihan()->belumDibayar()->sum('total_bayar');
    }

    // Method untuk mendapatkan info tarif
    public function getInfoTarif(): string
    {
        return $this->tarif ? "{$this->tarif->golongan}/{$this->tarif->daya}VA" : 'Belum ada tarif';
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Validasi nomor meter unique
        static::saving(function ($pelanggan) {
            $exists = static::where('nomor_meter', $pelanggan->nomor_meter)
                ->where('id', '!=', $pelanggan->id ?? 0)
                ->exists();

            if ($exists) {
                throw new \Exception('Nomor meter sudah digunakan');
            }
        });
    }
}