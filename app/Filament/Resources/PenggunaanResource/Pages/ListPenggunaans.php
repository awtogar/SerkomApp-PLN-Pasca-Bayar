<?php

namespace App\Filament\Resources\PenggunaanResource\Pages;

use App\Filament\Resources\PenggunaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenggunaans extends ListRecords
{
    protected static string $resource = PenggunaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
