<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Venta;
use Carbon\Carbon;

class PredictionTotalSalesWidget extends ChartWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = 'Predicción de Ventas';

    protected function getData(): array
    {
        $ventasUltimosDias = Venta::where('Fecha_Venta', '>=', Carbon::today()->subDays(4))
            ->selectRaw('DATE(Fecha_Venta) as fecha, SUM(Total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        if ($ventasUltimosDias->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        $ventasNumericas = $ventasUltimosDias->map(function ($venta, $index) {
            return [
                'fecha' => $venta->fecha,
                'x' => $index + 1, 
                'y' => $venta->total
            ];
        });

        $n = count($ventasNumericas);
        
        if ($n === 1) {
            $prediccionVentas = $ventasNumericas->first()['y'];
            $prediccionFecha = Carbon::today()->addDays(7)->toDateString();
            return [
                'labels' => [$ventasNumericas->first()['fecha'], $prediccionFecha],
                'datasets' => [
                    [
                        'label' => 'Ventas Actuales',
                        'data' => $ventasNumericas->pluck('y')->toArray(),
                        'borderColor' => 'rgba(0, 123, 255, 1)',
                        'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
                        'fill' => true,
                    ],
                    [
                        'label' => 'Predicción de Ventas',
                        'data' => [$prediccionVentas, $prediccionVentas],
                        'borderColor' => 'green',
                        'backgroundColor' => 'green',
                        'fill' => false,
                        'pointRadius' => 6,
                        'pointHoverRadius' => 8,
                    ]
                ],
                'prediccionVentas' => number_format($prediccionVentas, 2, ',', '.'),
                'gananciaEstimada' => number_format($prediccionVentas, 2, ',', '.'),
                'prediccionFecha' => $prediccionFecha,
            ];
        }

        $sumX = $ventasNumericas->sum('x');
        $sumY = $ventasNumericas->sum('y');
        $sumXY = $ventasNumericas->reduce(fn($carry, $item) => $carry + ($item['x'] * $item['y']), 0);
        $sumX2 = $ventasNumericas->reduce(fn($carry, $item) => $carry + ($item['x'] * $item['x']), 0);
        $denominador = ($n * $sumX2 - $sumX * $sumX);

        if ($denominador == 0) {
            return [
                'labels' => $ventasNumericas->pluck('fecha')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Ventas Actuales',
                        'data' => $ventasNumericas->pluck('y')->toArray(),
                        'borderColor' => 'rgba(0, 123, 255, 1)',
                        'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
                        'fill' => true,
                    ]
                ]
            ];
        }
        $m = ($n * $sumXY - $sumX * $sumY) / $denominador;
        $b = ($sumY - $m * $sumX) / $n;
        $prediccionFecha = Carbon::today()->addDays(7)->toDateString(); 
        $prediccionVentas = $m * ($n + 1) + $b; 
        $gananciaEstimada = $prediccionVentas;
        $labels = $ventasNumericas->pluck('fecha')->toArray();
        $labels[] = $prediccionFecha;

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ventas Actuales',
                    'data' => $ventasNumericas->pluck('y')->toArray(),
                    'borderColor' => 'rgba(0, 123, 255, 1)',
                    'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Predicción de Ventas',
                    'data' => array_merge(
                        $ventasNumericas->pluck('y')->toArray(),
                        [$prediccionVentas]
                    ),
                    'borderColor' => $prediccionVentas > $ventasNumericas->last()['y'] ? 'green' : 'red',
                    'backgroundColor' => $prediccionVentas > $ventasNumericas->last()['y'] ? 'green' : 'red',
                    'fill' => false,
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
                ],
                [
                    'label' => 'Ganancia Estimada (Próxima Semana) Bs.',
                    'data' => array_merge( 
                        $ventasNumericas->pluck('y')->toArray(),
                        [$gananciaEstimada]
                    ),
                    'borderColor' => 'orange',
                    'backgroundColor' => 'rgba(255, 165, 0, 0.5)',
                    'fill' => false,
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
                ]
            ],
            'prediccionVentas' => number_format($prediccionVentas, 2, ',', '.'),
            'gananciaEstimada' => number_format($gananciaEstimada, 2, ',', '.'),
            'prediccionFecha' => $prediccionFecha, 
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
