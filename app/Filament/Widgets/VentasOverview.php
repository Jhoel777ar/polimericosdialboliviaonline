<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentasOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $ventasData = DB::table('ventas')
            ->select(
                DB::raw('COUNT(*) as totalVentas'),
                DB::raw('SUM(CASE WHEN Estado = "enviado" THEN 1 ELSE 0 END) as enviados'),
                DB::raw('SUM(CASE WHEN Estado = "entregado" THEN 1 ELSE 0 END) as entregados'),
                DB::raw('SUM(CASE WHEN Estado = "pagado" THEN 1 ELSE 0 END) as pagados'),
                DB::raw('SUM(Total) as totalGanancias')
            )
            ->first();
        $ventasHoy = DB::table('ventas')
            ->whereDate('Fecha_Venta', Carbon::today())
            ->count();
        $totalVentas = $ventasData->totalVentas ?? 0;
        $enviados = $ventasData->enviados ?? 0;
        $entregados = $ventasData->entregados ?? 0;
        $pagados = $ventasData->pagados ?? 0;
        $totalGanancias = $ventasData->totalGanancias ?? 0;
        $ventasEnviadasHistorico = [0, $enviados];
        $ventasEntregadasHistorico = [0, $entregados];
        $ventasPagadasHistorico = [0, $pagados];

        $ventasLocalesData = DB::table('venta_locals')
            ->select(
                DB::raw('COUNT(*) as totalVentasLocales'),
                DB::raw('SUM(CASE WHEN estado = "entregado" THEN 1 ELSE 0 END) as entregadosLocales'),
                DB::raw('SUM(total) as totalGananciasLocales')
            )
            ->first();
        $ventasLocalesHoy = DB::table('venta_locals')
            ->whereDate('fecha_venta', Carbon::today())
            ->count();

        $totalVentasLocales = $ventasLocalesData->totalVentasLocales ?? 0;
        $totalGananciasLocales = $ventasLocalesData->totalGananciasLocales ?? 0;

        return [
            Stat::make('Total de Ventas en linea', $totalVentas)
                ->description('Total de todas las ventas')
                ->color($this->getColorForTotalVentas($totalVentas))
                ->chart([0, $totalVentas]),

            Stat::make('Ventas Enviadas en linea', $enviados)
                ->description('Ventas con estado enviado')
                ->color($this->getColorForTrend($ventasEnviadasHistorico))
                ->chart($ventasEnviadasHistorico),

            Stat::make('Ventas Entregadas en linea', $entregados)
                ->description('Ventas con estado entregado')
                ->color($this->getColorForTrend($ventasEntregadasHistorico))
                ->chart($ventasEntregadasHistorico),

            Stat::make('Ventas Pagadas en linea', $pagados)
                ->description('Ventas con estado pagado')
                ->color($this->getColorForTrend($ventasPagadasHistorico))
                ->chart($ventasPagadasHistorico),

            Stat::make('Total de Ganancias en linea', 'Bs. ' . number_format($totalGanancias, 2, ',', '.'))
                ->description('Suma total de las ganancias de todas las ventas')
                ->color('success')
                ->chart([0, $totalGanancias]),

            Stat::make('Ventas Realizadas Hoy en linea', $ventasHoy)
                ->description('Total de ventas realizadas hoy')
                ->color($ventasHoy > 0 ? 'success' : 'danger')
                ->chart([0, $ventasHoy]),

            Stat::make('Total de Ventas Locales', $totalVentasLocales)
                ->description('Total de todas las ventas locales')
                ->color($this->getColorForTotalVentas($totalVentasLocales))
                ->chart([0, $totalVentasLocales]),

            Stat::make('Total de Ganancias de Ventas Locales', 'Bs. ' . number_format($totalGananciasLocales, 2, ',', '.'))
                ->description('Suma total de las ganancias de todas las ventas locales')
                ->color('success')
                ->chart([0, $totalGananciasLocales]),

            Stat::make('Ventas Locales Realizadas Hoy', $ventasLocalesHoy)
                ->description('Total de ventas locales realizadas hoy')
                ->color($ventasLocalesHoy > 0 ? 'success' : 'danger')
                ->chart([0, $ventasLocalesHoy]),
        ];
    }

    private function getColorForTrend($historicalData)
    {
        $lastValue = $historicalData[1];
        return $lastValue > 0 ? 'success' : 'danger';
    }

    private function getColorForTotalVentas($totalVentas)
    {
        if ($totalVentas < 50) {
            return 'danger';
        } elseif ($totalVentas >= 50 && $totalVentas < 200) {
            return 'warning';
        } else {
            return 'success';
        }
    }
}
