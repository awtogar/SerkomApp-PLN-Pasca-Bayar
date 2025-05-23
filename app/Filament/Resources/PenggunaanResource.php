<?php
// /Users/awtogar/Development/tagihan-listrik/app/Filament/Resources/PenggunaanResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\PenggunaanResource\Pages;
use App\Models\Penggunaan;
use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PenggunaanResource extends Resource
{
    protected static ?string $model = Penggunaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    
    protected static ?string $navigationGroup = 'Pencatatan Penggunaan';
    public static function getPluralLabel(): string
    {
        return 'Penggunaan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_pelanggan')
                    ->label('Pelanggan')
                    ->options(Pelanggan::all()->pluck('nama_pelanggan', 'id'))
                    ->required()
                    ->reactive()
                    ->searchable(),

                Forms\Components\Select::make('bulan')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(now()->year),
                Forms\Components\TextInput::make('meter_awal')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(function ($get) {
                        $pelangganId = $get('id_pelanggan');
                        if (!$pelangganId) return 0;
                        // Ambil penggunaan terakhir untuk pelanggan ini
                        $last = Penggunaan::where('id_pelanggan', $pelangganId)
                            ->orderByDesc('tahun')
                            ->orderByDesc('bulan')
                            ->first();

                        return $last?->meter_akhir ?? 0;
                    }),
                     // Optional: disable biar ga bisa diubah manual
                    // ->disabled()
                    
                Forms\Components\TextInput::make('meter_akhir')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->gt('meter_awal'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->limit(32)
                    ->sortable(),
                Tables\Columns\TextColumn::make('bulan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('meter_awal')
                    ->numeric(),
                Tables\Columns\TextColumn::make('meter_akhir')
                    ->numeric(),
                Tables\Columns\TextColumn::make('jumlah_meter')
                    ->label('Total Meter')
                    ->getStateUsing(fn ($record) => $record->getJumlahMeter())
                    ->numeric(),
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
            'index' => Pages\ListPenggunaans::route('/'),
            'create' => Pages\CreatePenggunaan::route('/create'),
            'edit' => Pages\EditPenggunaan::route('/{record}/edit'),
        ];
    }
}