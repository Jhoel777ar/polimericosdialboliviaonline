<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InformacionOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        $totalUsuarios = DB::table('users')->count();
        $totalProductos = DB::table('productos')->count();
        $productosDisponibles = DB::table('productos')->where('estado', 'disponible')->count();
        $productosDescontinuados = DB::table('productos')->where('estado', 'descontinuado')->count();
        $totalCarritos = DB::table('carritos')->count();
        $totalEnvios = DB::table('envios')->count();
        $enviosPendientes = DB::table('envios')->where('Estado_Envio', 'pendiente')->count();
        $enviosEntregados = DB::table('envios')->where('Estado_Envio', 'entregado')->count();
        $totalSoportes = DB::table('soportes')->count();
        $soportesPendientes = DB::table('soportes')->where('status', 'pending')->count();
        $soportesResueltos = DB::table('soportes')->where('status', 'resolved')->count();
        $totalVentasEliminadas = DB::table('venta_eliminadas')->count();
        $totalCodigosQR = DB::table('codigo_qrs')->count();
        $totalCupones = DB::table('cupons')->count();
        $cuponesActivos = DB::table('cupons')->where('activo', 1)->count();
        $cuponesUsados = DB::table('cupons')->where('usos_actuales', '>', 0)->count();
        $totalComprobantes = DB::table('comprobante_pagos')->count();
        $comprobantesPendientes = DB::table('comprobante_pagos')->where('estado', 'pendiente')->count();
        $comprobantesAprobados = DB::table('comprobante_pagos')->where('estado', 'aprobado')->count();
        $ingresosPorMetodo = DB::table('ventas')
            ->select('Método_Pago', DB::raw('SUM(Total) as totalIngresos'))
            ->groupBy('Método_Pago')
            ->get();
        return [
            Stat::make('Usuarios Registrados', $totalUsuarios)
                ->description('Cantidad total de usuarios registrados')
                ->color('success')
                ->icon('heroicon-o-users')
                ->chart([0, $totalUsuarios]),
            Stat::make('Productos Disponibles', $productosDisponibles)
                ->description('Cantidad de productos disponibles')
                ->color('info')
                ->icon('heroicon-o-archive-box')
                ->chart([0, $productosDisponibles]),
            Stat::make('Productos Descontinuados', $productosDescontinuados)
                ->description('Cantidad de productos descontinuados')
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark')
                ->chart([0, $productosDescontinuados]),
            Stat::make('Total de Carritos', $totalCarritos)
                ->description('Cantidad total de carritos creados')
                ->color('warning')
                ->icon('heroicon-o-shopping-cart')
                ->chart([0, $totalCarritos]),
            Stat::make('Envíos Pendientes', $enviosPendientes)
                ->description('Cantidad de envíos pendientes')
                ->color('warning')
                ->icon('heroicon-o-truck')
                ->chart([0, $enviosPendientes]),
            Stat::make('Envíos Entregados', $enviosEntregados)
                ->description('Cantidad de envíos entregados')
                ->color('success')
                ->icon('heroicon-o-truck')
                ->chart([0, $enviosEntregados]),
            Stat::make('Soportes Pendientes', $soportesPendientes)
                ->description('Cantidad de soportes pendientes')
                ->color('danger')
                ->icon('heroicon-o-bug-ant')
                ->chart([0, $soportesPendientes]),
            Stat::make('Soportes Resueltos', $soportesResueltos)
                ->description('Cantidad de soportes resueltos')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->chart([0, $soportesResueltos]),
            Stat::make('Ventas Eliminadas', $totalVentasEliminadas)
                ->description('Cantidad de ventas eliminadas')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->chart([0, $totalVentasEliminadas]),
            Stat::make('Códigos QR Generados', $totalCodigosQR)
                ->description('Cantidad total de códigos QR generados')
                ->color('primary')
                ->icon('heroicon-o-qr-code')
                ->chart([0, $totalCodigosQR]),
            Stat::make('Cupones Activos', $cuponesActivos)
                ->description('Cantidad de cupones activos')
                ->color('success')
                ->icon('heroicon-o-gift')
                ->chart([0, $cuponesActivos]),
            Stat::make('Cupones Usados', $cuponesUsados)
                ->description('Cantidad de cupones usados')
                ->color('info')
                ->icon('heroicon-o-ticket')
                ->chart([0, $cuponesUsados]),
            Stat::make('Comprobantes Pendientes', $comprobantesPendientes)
                ->description('Cantidad de comprobantes de pago pendientes')
                ->color('warning')
                ->icon('heroicon-o-inbox-arrow-down')
                ->chart([0, $comprobantesPendientes]),
            Stat::make('Comprobantes Aprobados', $comprobantesAprobados)
                ->description('Cantidad de comprobantes de pago aprobados')
                ->color('success')
                ->icon('heroicon-o-check')
                ->chart([0, $comprobantesAprobados]),
            Stat::make('Ingresos por Método de Pago', $ingresosPorMetodo->pluck('totalIngresos')->sum())
                ->description('Total de ingresos agrupados por método de pago')
                ->color('primary')
                ->icon('heroicon-o-credit-card')
                ->chart(
                    $ingresosPorMetodo->map(function($item) {
                        return [$item->Método_Pago, $item->totalIngresos];
                    })->toArray()
                ),
        ];
    }
}
