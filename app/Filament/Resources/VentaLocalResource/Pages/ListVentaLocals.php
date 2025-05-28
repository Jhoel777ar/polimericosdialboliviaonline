<?php

namespace App\Filament\Resources\VentaLocalResource\Pages;

use App\Filament\Resources\VentaLocalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\VentaLocal;
use Filament\Resources\Components\Tab;

class ListVentaLocals extends ListRecords
{
    protected static string $resource = VentaLocalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'Total de Ventas' => Tab::make()
                ->query(fn($query) => $query->orderBy('fecha_venta'))
                ->badge(fn() => VentaLocal::count()), 
            'Total Ganado' => Tab::make()
                ->query(fn($query) => $query->orderBy('fecha_venta'))
                ->badge(fn() => 'Bs ' . number_format(VentaLocal::sum('total'), 2)), 
        ];
    }
}
