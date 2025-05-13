<?php

namespace App\Filament\Widgets;

use App\Models\Pelanggan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPelanggan extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pelanggan::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nomor_meter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('tarif.golongan_tarif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->heading('Pelanggan Terbaru');
    }
}