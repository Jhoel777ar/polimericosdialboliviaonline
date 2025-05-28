<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductosMasVendidos extends ChartWidget
{
    protected static ?int $sort = 6;
    protected static ?string $heading = 'Análisis de Productos Más Vendidos y Predicción';
    private int $paginaActual = 0;
    private int $productosPorPagina = 14;
    protected function getType(): string
    {
        return 'line';
    }
    protected function getData(): array
    {
        $fechaInicio = Carbon::now()->subMonths(6);
        $ventasPorMes = VentaDetalle::join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->where('ventas.Fecha_Venta', '>=', $fechaInicio)
            ->selectRaw('venta_detalles.producto_id, DATE_FORMAT(ventas.Fecha_Venta, "%Y-%m") as mes, SUM(venta_detalles.cantidad) as total_vendido')
            ->groupBy('venta_detalles.producto_id', 'mes')
            ->orderBy('mes')
            ->get();
        $ventasPorProducto = [];
        foreach ($ventasPorMes as $venta) {
            $ventasPorProducto[$venta->producto_id][$venta->mes] = $venta->total_vendido;
        }
        $topProductos = array_keys($ventasPorProducto);
        $labels = array_unique(array_column($ventasPorMes->toArray(), 'mes'));
        sort($labels);
        $productosPaginados = array_slice($topProductos, $this->paginaActual * $this->productosPorPagina, $this->productosPorPagina);
        if (empty($productosPaginados)) {
            $this->paginaActual = 0;
            $productosPaginados = array_slice($topProductos, 0, $this->productosPorPagina);
        }
        $datasets = [];
        foreach ($productosPaginados as $productoId) {
            $producto = Producto::find($productoId);
            if (!$producto) continue;

            $ventas = [];
            foreach ($labels as $mes) {
                $ventas[] = $ventasPorProducto[$productoId][$mes] ?? 0;
            }
            $prediccion = $this->regresionLineal($ventas);
            $datasets[] = [
                'label' => $producto->nombre,
                'data' => $prediccion,
                'borderColor' => sprintf('rgba(%d, %d, %d, 1)', rand(0, 255), rand(0, 255), rand(0, 255)),
                'fill' => false,
            ];
        }
        $this->paginaActual++;
        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }
    private function regresionLineal(array $datos): array
    {
        $n = count($datos);
        if ($n < 2) return $datos;
        $x = range(1, $n);
        $y = $datos;
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] ** 2;
        }
        $m = (($n * $sumXY) - ($sumX * $sumY)) / (($n * $sumX2) - ($sumX ** 2));
        $b = ($sumY - ($m * $sumX)) / $n;
        $predicciones = array_map(fn($xi) => round($m * $xi + $b, 2), $x);
        $predicciones[] = round($m * ($n + 1) + $b, 2);
        return $predicciones;
    }
}
