<?php
// /Users/awtogar/Development/tagihan-listrik/app/Filament/Resources/PelangganResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\PelangganResource\Pages;
use App\Filament\Resources\PelangganResource\RelationManagers;
use App\Models\Pelanggan;
use App\Models\Tarif;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Penggunaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel= 'Pelanggan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor_meter')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('nama_pelanggan')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('id_tarif')
                    ->label('Tarif')
                    ->options(Tarif::all()->pluck('golongan_tarif', 'id'))
                    ->required()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_meter')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tarif_info')
                    ->label('Kode Tarif')
                    ->getStateUsing(function ($record) {
                        $golongan = $record->tarif->golongan_tarif ?? '-';
                        $daya = $record->tarif->daya ?? '-';
                        return "{$golongan}/{$daya}VA";
                    })
                    ->sortable()
                    ->searchable(),
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
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->before(function ($record) {
                        // Hapus semua data terkait secara berurutan
                        $pelangganId = $record->id;
                        
                        // 1. Hapus pembayaran
                        Pembayaran::where('id_pelanggan', $pelangganId)->delete();
                        
                        // 2. Hapus tagihan
                        Tagihan::where('id_pelanggan', $pelangganId)->delete();
                        
                        // 3. Hapus penggunaan
                        Penggunaan::where('id_pelanggan', $pelangganId)->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                $pelangganId = $record->id;
                                
                                // 1. Hapus pembayaran
                                Pembayaran::where('id_pelanggan', $pelangganId)->delete();
                                
                                // 2. Hapus tagihan
                                Tagihan::where('id_pelanggan', $pelangganId)->delete();
                                
                                // 3. Hapus penggunaan
                                Penggunaan::where('id_pelanggan', $pelangganId)->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PenggunaanRelationManager::class,
            RelationManagers\TagihanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelanggan::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }
}