<?php

namespace App\Filament\Resources\VentaEliminadaResource\Pages;

use App\Filament\Resources\VentaEliminadaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\VentaEliminada;
use Filament\Resources\Components\Tab;

class ListVentaEliminadas extends ListRecords
{
    protected static string $resource = VentaEliminadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'Total de Ventas Eliminadas' => Tab::make()
                ->query(fn($query) => $query->orderBy('Fecha_Venta'))
                ->badge(fn() => VentaEliminada::count()),
            'Total perdido' => Tab::make()
                ->query(fn($query) => $query->orderBy('Fecha_Venta'))
                ->badge(fn() => 'Bs ' . number_format(VentaEliminada::sum('Total'), 2)),
            'Productos stock Agregado' => Tab::make()
                ->query(fn($query) => $query->orderBy('Fecha_Venta'))
                ->badge(fn() => $this->countDeletedProducts()),
        ];
    }

    protected function countDeletedProducts(): int
    {
        $totalCantidad = 0;
        $ventasEliminadas = VentaEliminada::all();
        foreach ($ventasEliminadas as $venta) {
            $detalles = $venta->detalles_venta; 

            foreach ($detalles as $detalle) {
                $totalCantidad += $detalle['cantidad'];
            }
        }

        return $totalCantidad;
    }
}
