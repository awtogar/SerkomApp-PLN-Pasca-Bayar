<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TarifResource\Pages;
use App\Models\Tagihan;
use App\Models\Tarif;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Components\Textarea;
use Illuminate\Validation\Rules\Unique;

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
        $golonganOptions = [
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

 return $form
        ->schema([
            TextInput::make('kode_tarif')
                ->label('Kode Tarif')
                ->readOnly()
                ->unique(ignoreRecord: true)
                ->columnSpan(4),

            Forms\Components\Select::make('golongan')
                ->label('Golongan Tarif')
                ->options($golonganOptions)
                ->live(debounce: 250)
                ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                    $daya = $get('daya');
                    if ($state && $daya) {
                        $slug = strtoupper(Str::slug($state . '-' . $daya));
                        $set('kode_tarif', $slug);
                    }
                })
                ->required()
                ->searchable()
                ->columnSpan(8),

            TextInput::make('daya')
                ->required()
                ->maxLength(20)
                ->suffix('VA')
                ->numeric()
                ->live()
                ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                    $golongan = $get('golongan');
                    if ($golongan && $state) {
                        $slug = strtoupper(Str::slug($golongan . '-' . $state));
                        $set('kode_tarif', $slug);
                    }
                })
                ->columnSpan(4),

            TextInput::make('tarif_perkwh')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->columnSpan(8),

            Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->required()
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
                    ->searchable()
                    ->sortable()
                    ->label('Kode Tarif'),


                Tables\Columns\TextColumn::make('golongan')
                    ->searchable()
                    ->sortable()
                    ->label('Golongan'),

                Tables\Columns\TextColumn::make('daya')
                    ->sortable()
                    ->suffix(' VA'),

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