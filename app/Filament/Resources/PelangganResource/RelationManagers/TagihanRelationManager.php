<?php
// /Users/awtogar/Development/tagihan-listrik/app/Filament/Resources/PelangganResource/RelationManagers/TagihanRelationManager.php
namespace App\Filament\Resources\PelangganResource\RelationManagers;

use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TagihanRelationManager extends RelationManager
{
    protected static string $relationship = 'tagihan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_penggunaan')
                    ->relationship('penggunaan', 'bulan')
                    ->required(),
                Forms\Components\TextInput::make('bulan')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(now()->year),
                Forms\Components\TextInput::make('jumlah_meter')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\Select::make('status')
                    ->options([
                        0 => 'Belum Dibayar',
                        1 => 'Sudah Dibayar',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('total_bayar')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bulan')
            ->columns([
                Tables\Columns\TextColumn::make('bulan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_meter')
                    ->numeric(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ((int) $state) {
                        0 => 'danger',
                        1 => 'success',
                        default => 'gray', // fallback biar ga error
                    })                    
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Belum Dibayar' : 'Sudah Dibayar'),
                Tables\Columns\TextColumn::make('total_bayar')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        0 => 'Belum Dibayar',
                        1 => 'Sudah Dibayar',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
