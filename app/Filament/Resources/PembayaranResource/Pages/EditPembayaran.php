<?php
namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\Tagihan;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPembayaran extends EditRecord
{
    protected static string $resource = PembayaranResource::class;

    // Hook yang dijalankan setelah pembayaran berhasil diupdate
    protected function afterSave(): void
    {
        // Pastikan status tagihan tetap "Sudah Dibayar" (1)
        $tagihan = Tagihan::find($this->record->id_tagihan);
        
        if ($tagihan && $tagihan->status !== 1) {
            $tagihan->update(['status' => 1]);
            
            // Kirim notifikasi sukses
            Notification::make()
                ->title('Pembayaran berhasil diperbarui')
                ->body("Status tagihan untuk {$tagihan->pelanggan->nama_pelanggan} telah diperbarui.")
                ->success()
                ->send();
        }
    }
}
