<?php
// /Users/awtogar/Development/tagihan-listrik/app/Filament/Resources/PelangganResource/RelationManagers/TagihanRelationManager.php
namespace App\Filament\Resources\PelangganResource\RelationManagers;

use App\Models\Tagihan;
use App\Models\Penggunaan;
use App\Models\Pelanggan;
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
                    ->label('Penggunaan')
                    ->options(function () {
                        $pelangganId = $this->ownerRecord->id;
                        
                        return Penggunaan::query()
                            ->where('id_pelanggan', $pelangganId)
                            ->whereDoesntHave('tagihan')
                            ->get()
                            ->mapWithKeys(function ($penggunaan) {
                                $jumlahMeter = $penggunaan->getJumlahMeter();
                                return [
                                    $penggunaan->id => "Bulan: {$penggunaan->bulan} {$penggunaan->tahun} - {$jumlahMeter} kWh"
                                ];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $penggunaan = Penggunaan::find($state);
                            
                            if ($penggunaan) {
                                $set('bulan', $penggunaan->bulan);
                                $set('tahun', $penggunaan->tahun);
                                $set('jumlah_meter', $penggunaan->getJumlahMeter());
                                
                                // Hitung total bayar berdasarkan tarif pelanggan
                                $pelanggan = $this->ownerRecord;
                                if ($pelanggan && $pelanggan->tarif) {
                                    $totalBayar = $penggunaan->getJumlahMeter() * $pelanggan->tarif->tarif_perkwh;
                                    $set('total_bayar', $totalBayar);
                                }
                            }
                        }
                    }),
                
                Forms\Components\TextInput::make('bulan')
                    ->required()
                    ->maxLength(20)
                    ->disabled()
                    ->dehydrated(),
                
                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                
                Forms\Components\TextInput::make('jumlah_meter')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                
                Forms\Components\TextInput::make('total_bayar')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                
                Forms\Components\Select::make('status')
                    ->options([
                        0 => 'Belum Dibayar',
                        1 => 'Sudah Dibayar',
                    ])
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bulan')
            ->columns([
                Tables\Columns\TextColumn::make('bulan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_meter')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_bayar')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->getStateUsing(fn ($record) => $record->getStatusText())
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sudah Dibayar' => 'success',
                        'Belum Dibayar' => 'danger',
                    }),
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