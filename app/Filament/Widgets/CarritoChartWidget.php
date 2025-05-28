<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CarritoChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Clientes Recurrentes vs. Nuevos';
    protected static ?int $sort = 7;
    protected function getData(): array
    {
        $recurrentes = DB::table('ventas')
            ->selectRaw('ID_Usuario, COUNT(*) as total')
            ->groupBy('ID_Usuario')
            ->having('total', '>', 1)
            ->count();
        $nuevos = DB::table('ventas')
            ->selectRaw('COUNT(DISTINCT ID_Usuario) as total')
            ->value('total') - $recurrentes;
        return [
            'labels' => ['Recurrentes', 'Nuevos'],
            'datasets' => [
                [
                    'data' => [$recurrentes, $nuevos],
                    'backgroundColor' => ['#36a2eb', '#ffce56'],
                ],
            ],
        ];
    }
    protected function getType(): string
    {
        return 'doughnut';
    }
}
