<?php

namespace App\Filament\Resources;
use App\Filament\Resources\PenggunaanResource\Pages;
use App\Models\Penggunaan;
use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;

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
                    ->searchable()
                    ->live(),

                Forms\Components\Select::make('bulan')
                    ->label('Bulan')
                    ->options(function (Get $get) {
                        $idPelanggan = $get('id_pelanggan');
                        $tahun = $get('tahun');

                        if (!$idPelanggan || !$tahun) {
                            return [];
                        }

                        $bulanTerpakai = Penggunaan::where('id_pelanggan', $idPelanggan)
                            ->where('tahun', $tahun)
                            ->pluck('bulan')
                            ->toArray();

                        $semuaBulan = [
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
                        ];

                        return collect($semuaBulan)
                            ->reject(fn($label, $key) => in_array($key, $bulanTerpakai))
                            ->toArray();
                    })
                    ->required()
                    ->live(),


                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric()
                    ->default(function (Get $get) {
                        $idPelanggan = $get('id_pelanggan');
                        if (!$idPelanggan) {
                            return now()->year;
                        }

                        $tahunTerakhir = Penggunaan::where('id_pelanggan', $idPelanggan)
                            ->orderByDesc('tahun')
                            ->value('tahun');

                        return $tahunTerakhir ?? now()->year;
                    }),


                Forms\Components\TextInput::make('meter_awal')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->label('Meter Awal')
                    ->helperText('Otomatis diisi berdasarkan meter akhir bulan sebelumnya')
                    // ->readOnly()
                    ->dehydrated(),

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
                    ->sortable(),

                Tables\Columns\TextColumn::make('pelanggan.nomor_meter')
                    ->label('No. Meter')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bulan')
                    ->label('Periode')
                    ->getStateUsing(function (Penggunaan $record): string {
                        // Format bulan menjadi 2 digit (01, 02, dst) - Same as RelationManager
                        $bulan = str_pad($record->bulan, 2, '0', STR_PAD_LEFT);
                        return "{$bulan}/{$record->tahun}";
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('meter_awal')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('meter_akhir')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_meter')
                    ->label('Total Meter')
                    ->getStateUsing(fn($record) => $record->getJumlahMeter())
                    ->numeric()
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
                Tables\Filters\SelectFilter::make('id_pelanggan')
                    ->label('Pelanggan')
                    ->options(Pelanggan::all()->pluck('nama_pelanggan', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('bulan')
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
                    ]),

                Tables\Filters\Filter::make('tahun')
                    ->form([
                        Forms\Components\TextInput::make('tahun')
                            ->numeric()
                            ->placeholder('2024'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['tahun'],
                            fn($query, $tahun) => $query->where('tahun', $tahun)
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
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
