<?php
// /Users/awtogar/Development/tagihan-listrik/app/Filament/Resources/PelangganResource/RelationManagers/PenggunaanRelationManager.php
namespace App\Filament\Resources\PelangganResource\RelationManagers;

use App\Models\Penggunaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Tables\Filters;
use App\Models\Pelanggan;

class PenggunaanRelationManager extends RelationManager
{
    protected static string $relationship = 'penggunaan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->default(function () {
                        $pelangganId = $this->ownerRecord->id;
                        
                        // Ambil penggunaan terakhir untuk pelanggan ini
                        $last = Penggunaan::where('id_pelanggan', $pelangganId)
                            ->orderByDesc('tahun')
                            ->orderByDesc('bulan')
                            ->first();

                        return $last?->meter_akhir ?? 0;
                    }),
                    // ->disabled(),
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
