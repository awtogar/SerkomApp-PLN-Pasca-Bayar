<?php

namespace App\Filament\Resources\PenggunaanResource\Pages;

use App\Filament\Resources\PenggunaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenggunaan extends EditRecord
{
    protected static string $resource = PenggunaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
