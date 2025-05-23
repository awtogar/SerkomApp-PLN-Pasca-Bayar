<?php
namespace App\Models;
// /Users/awtogar/Development/tagihan-listrik/app/Models/tagihan.php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    
    protected $fillable = [
        'id_pelanggan',
        'id_penggunaan',
        'bulan',
        'tahun',
        'jumlah_meter',
        'total_bayar',
        'status',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'jumlah_meter' => 'integer',
        'total_bayar' => 'decimal:2',
        'status' => 'integer',
    ];

    const STATUS_BELUM_DIBAYAR = 0;
    const STATUS_SUDAH_DIBAYAR = 1;

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
        return $this->status == self::STATUS_SUDAH_DIBAYAR ? 'Sudah Dibayar' : 'Belum Dibayar';
    }

    // Scope untuk tagihan yang belum dibayar
    public function scopeBelumDibayar(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_BELUM_DIBAYAR);
    }

    // Scope untuk tagihan yang sudah dibayar
    public function scopeSudahDibayar(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUDAH_DIBAYAR);
    }

    // Method untuk mendapatkan periode
    public function getPeriodeAttribute(): string
    {
        $bulan = str_pad($this->bulan, 2, '0', STR_PAD_LEFT);
        return "{$bulan}/{$this->tahun}";
    }

    // Method untuk format total bayar
    public function getFormattedTotalBayar(): string
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Auto-fill data dari penggunaan saat membuat tagihan
        static::creating(function ($tagihan) {
            if ($tagihan->id_penggunaan && !$tagihan->bulan) {
                $penggunaan = Penggunaan::find($tagihan->id_penggunaan);
                if ($penggunaan) {
                    $tagihan->bulan = $penggunaan->bulan;
                    $tagihan->tahun = $penggunaan->tahun;
                    $tagihan->jumlah_meter = $penggunaan->getJumlahMeter();
                    
                    // Hitung total bayar berdasarkan tarif
                    $pelanggan = $penggunaan->pelanggan;
                    if ($pelanggan && $pelanggan->tarif) {
                        $tagihan->total_bayar = $penggunaan->getJumlahMeter() * $pelanggan->tarif->tarif_perkwh;
                    }
                }
            }
        });
    }
}
