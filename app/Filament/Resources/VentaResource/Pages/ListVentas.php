<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Venta;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Total de ventas ' => Tab::make()
                ->query(fn($query) => $query->orderBy('Fecha_Venta'))
                ->badge(function () {
                    return Venta::count();
                }),
            'Pendiente' => Tab::make()
                ->query(fn($query) => $query->where('Estado', 'pendiente')->orderBy('Fecha_Venta'))
                ->badge(function () {
                    return Venta::where('Estado', 'pendiente')->count();
                }),
            'Pagado' => Tab::make()
                ->query(fn($query) => $query->where('Estado', 'pagado')->orderBy('Fecha_Venta'))
                ->badge(function () {
                    return Venta::where('Estado', 'pagado')->count();
                }),
            'Enviado' => Tab::make()
                ->query(fn($query) => $query->where('Estado', 'enviado')->orderBy('Fecha_Venta'))
                ->badge(function () {
                    return Venta::where('Estado', 'enviado')->count();
                }),
            'Entregado' => Tab::make()
                ->query(fn($query) => $query->where('Estado', 'entregado')->orderBy('Fecha_Venta'))
                ->badge(function () {
                    return Venta::where('Estado', 'entregado')->count();
                }),
        ];
    }
}
