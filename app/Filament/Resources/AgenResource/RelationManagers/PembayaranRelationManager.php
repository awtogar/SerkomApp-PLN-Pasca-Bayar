<?php
// app/Filament/Resources/AgenResource/RelationManagers/PembayaranRelationManager.php
namespace App\Filament\Resources\AgenResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PembayaranRelationManager extends RelationManager
{
    protected static string $relationship = 'pembayaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_tagihan')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal_pembayaran')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tagihan.bulan')
                    ->label('Bulan Tagihan'),
                Tables\Columns\TextColumn::make('tagihan.tahun')
                    ->label('Tahun Tagihan'),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')
                    ->date(),
                Tables\Columns\TextColumn::make('total_bayar')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
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