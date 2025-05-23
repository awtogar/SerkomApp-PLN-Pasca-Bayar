<?php
// /Users/awtogar/Development/tagihan-listrik/app/Filament/Resources/PembayaranResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranResource\Pages;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Agen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Transaksi Pembayaran';
    protected static ?int $navigationSort = 6;

    public static function getPluralLabel(): string
    {
        return 'Pembayaran';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_tagihan')
                    ->label('Tagihan (Nomor Meter - Pelanggan)')
                    ->options(function () {
                        $query = Tagihan::where('status', 0)->whereDoesntHave('pembayaran');
                        
                        // Filter jika ada parameter tagihan_id dari URL
                        $tagihanId = request()->query('tagihan_id');
                        if ($tagihanId) {
                            $query->where('id', $tagihanId);
                        }
                        
                        return $query->with('pelanggan')->get()->mapWithKeys(function ($tagihan) {
                            $pelanggan = $tagihan->pelanggan;
                            return [
                                $tagihan->id => "{$pelanggan->nama_pelanggan}({$pelanggan->nomor_meter})",
                            ];
                        });
                    })
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $tagihan = Tagihan::with('pelanggan')->find($state);
                            if ($tagihan) {
                                $set('id_pelanggan', $tagihan->id_pelanggan);
                                $set('bulan_bayar', $tagihan->bulan);
                                $set('tahun_bayar', $tagihan->tahun);
                                $set('total_bayar', $tagihan->total_bayar);
                            }
                        }
                    }),
                Forms\Components\Select::make('id_pelanggan')
                    ->label('Pelanggan')
                    ->options(Pelanggan::all()->pluck('nama_pelanggan', 'id'))
                    ->required()
                    ->searchable()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\DatePicker::make('tanggal_pembayaran')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('bulan_bayar')
                    ->required()
                    ->maxLength(20)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('tahun_bayar')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('biaya_admin')
                    ->required()
                    ->numeric()
                    ->default(2500)
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('total_bayar')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Select::make('id_agen')
                    ->label('Agen')
                    ->options(Agen::all()->pluck('nama_agen', 'id'))
                    ->required()
                    ->searchable(),
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
                    ->label('Nomor Meter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periode_tagihan')
                    ->label('Periode Tagihan')
                    ->getStateUsing(function (Pembayaran $record): string {
                        // Format bulan menjadi 2 digit (01, 02, dst)
                        $bulan = str_pad($record->bulan_bayar, 2, '0', STR_PAD_LEFT);
                        return "{$bulan}/{$record->tahun_bayar}";
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_keseluruhan')
                    ->label('Total Bayar')
                    ->getStateUsing(function (Pembayaran $record): string {
                        $total = $record->total_bayar + $record->biaya_admin;
                        return 'Rp ' . number_format($total, 0, ',', '.');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_admin')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('agen.nama_agen')
                    ->label('Agen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal_pembayaran')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date) => $query->whereDate('tanggal_pembayaran', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date) => $query->whereDate('tanggal_pembayaran', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('id_agen')
                    ->label('Agen')
                    ->options(Agen::all()->pluck('nama_agen', 'id')),
            ])
            ->actions([
                Action::make('cetak_struk')
                    ->label('Cetak Struk')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (Pembayaran $record) => route('struk.pembayaran', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // Kembalikan status tagihan menjadi belum dibayar
                        if ($record->tagihan) {
                            $record->tagihan->update(['status' => 0]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                // Kembalikan status tagihan menjadi belum dibayar
                                if ($record->tagihan) {
                                    $record->tagihan->update(['status' => 0]);
                                }
                            }
                        }),
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
            'index' => Pages\ListPembayaran::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
    
    // Method untuk menangani logika setelah pembayaran dibuat
    public static function afterCreate(Model $record): void
    {
        // Update status tagihan menjadi "Sudah Dibayar" (1)
        $tagihan = Tagihan::find($record->id_tagihan);
        
        if ($tagihan) {
            $tagihan->update(['status' => 1]);
            
            // Kirim notifikasi sukses
            Notification::make()
                ->title('Pembayaran berhasil dibuat')
                ->body("Pembayaran untuk {$tagihan->pelanggan->nama_pelanggan} berhasil dicatat dan status tagihan diperbarui.")
                ->success()
                ->send();
        }
    }
}