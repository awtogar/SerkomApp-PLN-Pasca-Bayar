<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AgenResource\Pages;
use App\Filament\Resources\AgenResource\RelationManagers;
use App\Models\Agen;
use App\Models\Pembayaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class AgenResource extends Resource
{
    protected static ?string $model = Agen::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Transaksi Pembayaran';
    protected static ?int $navigationSort = 5;
    public static function getPluralLabel(): string
    {
        return 'Agen';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),   
            
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                
                Forms\Components\TextInput::make('nama_agen')
                    ->label('Nama')
                    ->required()
                    ->maxLength(100),
                
                Forms\Components\Textarea::make('alamat_agen')
                    ->required()
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('no_telepon')
                    ->tel()
                    ->required()
                    ->maxLength(15),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('nama_agen')
                ->label('Nama')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('alamat_agen')
                      ->label('Alamat')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('no_telepon')
                      ->label('No Telepon')
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
                        // Hapus semua pembayaran terkait dengan agen ini
                        Pembayaran::where('id_agen', $record->id)->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Hapus semua pembayaran terkait dengan semua agen yang akan dihapus
                            foreach ($records as $record) {
                                Pembayaran::where('id_agen', $record->id)->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PembayaranRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgen::route('/'),
            'create' => Pages\CreateAgen::route('/create'),
            'edit' => Pages\EditAgen::route('/{record}/edit'),
        ];
    }
}