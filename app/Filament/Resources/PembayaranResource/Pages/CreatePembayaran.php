<?php
namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\Tagihan;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePembayaran extends CreateRecord
{
    protected static string $resource = PembayaranResource::class;

    // Hook yang dijalankan setelah pembayaran berhasil dibuat
    protected function afterCreate(): void
    {
        // Update status tagihan menjadi "Sudah Dibayar" (1)
        $tagihan = Tagihan::find($this->record->id_tagihan);
        
        if ($tagihan) {
            $tagihan->update(['status' => 1]);
            
            // Kirim notifikasi sukses
            Notification::make()
                ->title('Pembayaran berhasil dibuat')
                ->body("Pembayaran untuk {$tagihan->pelanggan->nama_pelanggan} berhasil dicatat dan status tagihan diperbarui.")
                ->success()
                ->send();
        }
    }
}
