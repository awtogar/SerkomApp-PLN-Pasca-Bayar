<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TarifResource\Pages;
use App\Models\Tarif;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TarifResource extends Resource
{
    protected static ?string $model = Tarif::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Informasi';
    protected static ?int $navigationSort = 1;

    public static function getPluralLabel(): string
    {
        return 'Tarif';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_tarif')
                    ->label('Kode Tarif')
                    ->readOnly()
                    ->unique(ignoreRecord: true)
                    ->columnSpan(4),

                Select::make('golongan')
                    ->label('Golongan Tarif')
                    ->options(self::getGolonganOptions())
                    ->live(debounce: 250)
                    ->afterStateUpdated(fn(Set $set, Get $get) =>
                        $set('kode_tarif', self::generateKodeTarif($get('golongan'), $get('daya')))
                    )
                    ->required()
                    ->searchable()
                    ->columnSpan(8),

                TextInput::make('daya')
                    ->required()
                    ->suffix('VA')
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(fn(Set $set, Get $get) =>
                        $set('kode_tarif', self::generateKodeTarif($get('golongan'), $get('daya')))
                    )
                    ->columnSpan(4),

                TextInput::make('tarif_perkwh')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->columnSpan(8),

                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->maxLength(255)
                    ->columnSpan(12),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_tarif')
                    ->label('Kode Tarif')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('golongan')
                    ->label('Golongan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('daya')
                    ->suffix(' VA')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tarif_perkwh')
                    ->label('Tarif per kWh')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function generateKodeTarif(?string $golongan, ?string $daya): string
    {
        if (!$golongan || !$daya) return '';
        return strtoupper("{$golongan}/{$daya}VA");
    }

    public static function getGolonganOptions(): array
    {
        return [
            'R1' => 'R1 - Rumah Tangga (450 VA - 2.200 VA)',
            'R2' => 'R2 - Rumah Tangga (3.500 VA - 5.500 VA)',
            'R3' => 'R3 - Rumah Tangga (di atas 6.600 VA)',
            'B1' => 'B1 - Bisnis Kecil (di bawah 6.600 VA)',
            'B2' => 'B2 - Bisnis Menengah (6.600 VA - 200 kVA)',
            'B3' => 'B3 - Bisnis Besar (di atas 200 kVA)',
            'I1' => 'I1 - Industri/UMKM (450 VA - 14 kVA)',
            'I2' => 'I2 - Industri Kecil (14 kVA - 200 kVA)',
            'I3' => 'I3 - Industri Menengah (200 kVA - 30 MVA)',
            'I4' => 'I4 - Industri Besar (di atas 30 MVA)',
            'P1' => 'P1 - Pemerintah Kecil (6.600 VA - 200 kVA)',
            'P2' => 'P2 - Pemerintah Besar (di atas 200 kVA)',
            'P3' => 'P3 - Penerangan Jalan Umum (PJU)',
        ];
    }

    public static function getRelations(): array
    {
        return [];
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
