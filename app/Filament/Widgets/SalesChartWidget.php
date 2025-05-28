<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Ventas de los Últimos 4 Días';

    protected function getData(): array
    {
        $cacheKey = 'ventas_ultimos_4_dias';
        $ventas = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return Venta::where('Fecha_Venta', '>=', Carbon::today()->subDays(4))
                ->selectRaw('DATE(Fecha_Venta) as fecha, SUM(Total) as total')
                ->groupBy(DB::raw('DATE(Fecha_Venta)'))
                ->orderBy(DB::raw('DATE(Fecha_Venta)'))
                ->get();
        });

        $labels = [];
        $data = [];
        $fechas = collect();
        for ($i = 3; $i >= 0; $i--) {
            $fechas->push(Carbon::today()->subDays($i)->toDateString());
        }

        foreach ($fechas as $fecha) {
            $labels[] = $fecha;
            $data[] = 0;
        }

        foreach ($ventas as $venta) {
            $fechaIndex = $fechas->search($venta->fecha);
            if ($fechaIndex !== false) {
                $data[$fechaIndex] = (float) $venta->total;
            }
        }

        if (empty($labels) || empty($data)) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total de Ventas',
                        'data' => [0],
                        'backgroundColor' => '#FF6347',
                        'borderColor' => '#FF6347',
                        'borderWidth' => 1,
                    ],
                ],
                'labels' => ['Sin Datos'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas de los Últimos 4 Días',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'borderWidth' => 1,
                    'hoverBackgroundColor' => '#FF5733',
                    'hoverBorderColor' => '#FF5733',
                    'barPercentage' => 0.8,
                    'categoryPercentage' => 0.9,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        return 'Este gráfico muestra el total de ventas de los últimos 4 días, con las fechas correspondientes.';
    }
}
