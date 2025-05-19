<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembayaran extends ListRecords
{
    protected static string $resource = PembayaranResource::class;
    public static function getNavigationLabel(): string
{
    return 'Pembayaran';
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
