<?php
// Perbaikan untuk Model Penggunaan
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

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

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'meter_awal' => 'integer',
        'meter_akhir' => 'integer',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class, 'id_penggunaan');
    }

    public function getJumlahMeter(): int
    {
        return $this->meter_akhir - $this->meter_awal;
    }

    // Scope untuk mencari penggunaan berdasarkan periode
    public function scopeByPeriode(Builder $query, int $bulan, int $tahun): Builder
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }

    // Scope untuk mencari penggunaan yang belum memiliki tagihan
    public function scopeBelumAdaTagihan(Builder $query): Builder
    {
        return $query->whereDoesntHave('tagihan');
    }

    // Method untuk mendapatkan nama bulan
    public function getNamaBulan(): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $namaBulan[$this->bulan] ?? 'Unknown';
    }

    // Method untuk mendapatkan periode format string
    public function getPeriodeAttribute(): string
    {
        $bulan = str_pad($this->bulan, 2, '0', STR_PAD_LEFT);
        return "{$bulan}/{$this->tahun}";
    }

    // Method untuk validasi meter
    public function isValidMeter(): bool
    {
        return $this->meter_akhir >= $this->meter_awal;
    }

    // Method untuk mendapatkan penggunaan sebelumnya
    public function getPenggunaanSebelumnya(): ?self
    {
        if ($this->bulan == 1) {
            return static::where('id_pelanggan', $this->id_pelanggan)
                ->where('bulan', 12)
                ->where('tahun', $this->tahun - 1)
                ->first();
        }
        
        return static::where('id_pelanggan', $this->id_pelanggan)
            ->where('bulan', $this->bulan - 1)
            ->where('tahun', $this->tahun)
            ->first();
    }

    // Method untuk mendapatkan penggunaan selanjutnya
    public function getPenggunaanSelanjutnya(): ?self
    {
        if ($this->bulan == 12) {
            return static::where('id_pelanggan', $this->id_pelanggan)
                ->where('bulan', 1)
                ->where('tahun', $this->tahun + 1)
                ->first();
        }
        
        return static::where('id_pelanggan', $this->id_pelanggan)
            ->where('bulan', $this->bulan + 1)
            ->where('tahun', $this->tahun)
            ->first();
    }

    // Boot method untuk validasi otomatis
    protected static function boot()
    {
        parent::boot();

        // Validasi sebelum save
        static::saving(function ($penggunaan) {
            // Validasi meter akhir harus >= meter awal
            if ($penggunaan->meter_akhir < $penggunaan->meter_awal) {
                throw new \Exception('Meter akhir tidak boleh lebih kecil dari meter awal');
            }

            // Validasi duplikasi periode untuk pelanggan yang sama
            $exists = static::where('id_pelanggan', $penggunaan->id_pelanggan)
                ->where('bulan', $penggunaan->bulan)
                ->where('tahun', $penggunaan->tahun)
                ->where('id', '!=', $penggunaan->id ?? 0)
                ->exists();

            if ($exists) {
                throw new \Exception('Data penggunaan untuk periode ini sudah ada');
            }
        });

        // Update meter awal penggunaan selanjutnya ketika meter akhir diubah
        static::updated(function ($penggunaan) {
            $penggunaanSelanjutnya = $penggunaan->getPenggunaanSelanjutnya();
            if ($penggunaanSelanjutnya) {
                $penggunaanSelanjutnya->update([
                    'meter_awal' => $penggunaan->meter_akhir
                ]);
            }
        });
    }
}