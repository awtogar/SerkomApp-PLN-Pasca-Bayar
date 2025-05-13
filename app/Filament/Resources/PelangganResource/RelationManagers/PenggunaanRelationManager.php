<?php
// /Users/awtogar/Development/tagihan-listrik/app/Filament/Resources/PelangganResource/RelationManagers/PenggunaanRelationManager.php
namespace App\Filament\Resources\PelangganResource\RelationManagers;

use App\Models\Penggunaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PenggunaanRelationManager extends RelationManager
{
    protected static string $relationship = 'penggunaan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('bulan')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(now()->year),
                Forms\Components\TextInput::make('meter_awal')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('meter_akhir')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->gt('meter_awal'),
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
                Tables\Columns\TextColumn::make('meter_awal')
                    ->numeric(),
                Tables\Columns\TextColumn::make('meter_akhir')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_meter')
                ->label('Total Meter')
                ->numeric()
                ->getStateUsing(fn ($record) => $record->getJumlahMeter()),                
            ])
            ->filters([
                //
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