<?php

namespace App\Filament\Widgets;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pelanggan', Pelanggan::count())
                ->description('Jumlah semua pelanggan')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            
            Stat::make('Tagihan Belum Dibayar', Tagihan::where('status', 'belum_bayar')->count())
                ->description('Tagihan yang belum lunas')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
                
            Stat::make('Total Pembayaran', 'Rp ' . number_format(Pembayaran::sum('total_bayar'), 0, ',', '.'))
                ->description('Total pendapatan')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}