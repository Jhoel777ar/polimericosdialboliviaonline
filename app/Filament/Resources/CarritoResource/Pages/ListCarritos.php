<?php

namespace App\Filament\Resources\CarritoResource\Pages;

use App\Filament\Resources\CarritoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Carrito;
use Carbon\Carbon;

class ListCarritos extends ListRecords
{
    protected static string $resource = CarritoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make()
                ->query(fn($query) => $query->orderBy('Fecha_Agregado'))
                ->badge(function () {
                    return Carrito::count();
                }),
            'Agregadas al Carrito Hoy' => Tab::make()
                ->query(function ($query) {
                    return $query->whereDate('Fecha_Agregado', Carbon::today());
                })
                ->badge(function () {
                    return Carrito::whereDate('Fecha_Agregado', Carbon::today())->count();
                }),
        ];
    }
}
