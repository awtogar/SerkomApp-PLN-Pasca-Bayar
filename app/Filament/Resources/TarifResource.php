<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TarifResource\Pages;
use App\Models\Tarif;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TarifResource extends Resource
{
    protected static ?string $model = Tarif::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_tarif')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->label('Kode Tarif'),
                Forms\Components\TextInput::make('golongan_tarif')
                    ->required()
                    ->maxLength(50)
                    ->label('Golongan Tarif'),
                Forms\Components\TextInput::make('daya')
                    ->required()
                    ->maxLength(20)
                    ->suffix('VA'),
                Forms\Components\TextInput::make('tarif_perkwh')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Tarif per kWh dalam Rupiah'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_tarif')
                    ->searchable()
                    ->sortable()
                    ->label('Kode Tarif'),
                Tables\Columns\TextColumn::make('golongan_tarif')
                    ->searchable()
                    ->sortable()
                    ->label('Golongan Tarif'),
                Tables\Columns\TextColumn::make('daya')
                    ->sortable()
                    ->suffix(' VA'),
                Tables\Columns\TextColumn::make('tarif_perkwh')
                    ->money('IDR')
                    ->sortable()
                    ->label('Tarif per kWh'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTarifs::route('/'),
            'create' => Pages\CreateTarif::route('/create'),
            'edit' => Pages\EditTarif::route('/{record}/edit'),
        ];
    }
}