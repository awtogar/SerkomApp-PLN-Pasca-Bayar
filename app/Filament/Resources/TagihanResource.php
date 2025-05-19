<?php
// /Users/awtogar/Development/tagihan-listrik/app/Filament/Resources/TagihanResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\TagihanResource\Pages;
use App\Models\Penggunaan;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TagihanResource extends Resource
{
    protected static ?int $navigationSort = 2;
    protected static ?string $model = Tagihan::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    public static function getPluralLabel(): string
    {
        return 'Tagihan';
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
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('id_penggunaan', null)),
                
                Forms\Components\Select::make('id_penggunaan')
                    ->label('Penggunaan')
                    ->options(function (callable $get) {
                        $pelangganId = $get('id_pelanggan');
                        
                        if (!$pelangganId) {
                            return [];
                        }
                        
                        return Penggunaan::query()
                            ->where('id_pelanggan', $pelangganId)
                            ->whereDoesntHave('tagihan')
                            ->get()
                            ->mapWithKeys(function ($penggunaan) {
                                $jumlahMeter = $penggunaan->getJumlahMeter();
                                return [
                                    $penggunaan->id => "Bulan: {$penggunaan->bulan} {$penggunaan->tahun} - Meter: {$jumlahMeter}"
                                ];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state) {
                            $penggunaan = Penggunaan::find($state);
                            
                            if ($penggunaan) {
                                $set('bulan', $penggunaan->bulan);
                                $set('tahun', $penggunaan->tahun);
                                $set('jumlah_meter', $penggunaan->getJumlahMeter());
                                
                                // Hitung total bayar berdasarkan tarif pelanggan
                                $pelanggan = Pelanggan::find($get('id_pelanggan'));
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                
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
            'index' => Pages\ListTagihans::route('/'),
            'create' => Pages\CreateTagihan::route('/create'),
            'edit' => Pages\EditTagihan::route('/{record}/edit'),
        ];
    }
}
