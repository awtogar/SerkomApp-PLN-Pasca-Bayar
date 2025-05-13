<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PembayaranChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pembayaran Bulanan';

    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getData(): array
    {
        $data = $this->getPembayaranData();
        
        return [
            'datasets' => [
                [
                    'label' => 'Pembayaran Bulanan',
                    'data' => $data['values'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    private function getPembayaranData(): array
    {
        $now = Carbon::now();
        $months = collect();
        $values = collect();
        
        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthName = $month->format('M Y');
            $months->push($monthName);
            
            // $total = Pembayaran::whereYear('tanggal_bayar', $month->year)
            //     ->whereMonth('tanggal_bayar', $month->month)
            //     ->sum('total_bayar');
                
            // $values->push($total);
        }
        
        return [
            'labels' => $months->toArray(),
            'values' => $values->toArray(),
        ];
    }
}