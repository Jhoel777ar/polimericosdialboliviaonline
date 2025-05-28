<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Producto;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Total' => Tab::make()
                ->query(fn($query) => $query->orderBy('nombre'))
                ->badge(function () {
                    return Producto::count();
                }),
            'Stock Disponible' => Tab::make()
                ->query(fn($query) => $query->where('stock', '>', 0)->orderBy('nombre'))
                ->badge(function () {
                    return Producto::where('stock', '>', 0)->count();
                }),
            'Stock No Disponible' => Tab::make()
                ->query(fn($query) => $query->where('stock', '=', 0)->orderBy('nombre'))
                ->badge(function () {
                    return Producto::where('stock', '=', 0)->count();
                }),
            'Disponible' => Tab::make()
                ->query(fn($query) => $query->where('estado', 'disponible')->orderBy('nombre'))
                ->badge(function () {
                    return Producto::where('estado', 'disponible')->count();
                }),
            'Descontinuado' => Tab::make()
                ->query(fn($query) => $query->where('estado', 'descontinuado')->orderBy('nombre'))
                ->badge(function () {
                    return Producto::where('estado', 'descontinuado')->count();
                }),
            'Activo' => Tab::make('Activo')
                ->query(fn($query) => $query->where('activo', 1)->orderBy('nombre'))
                ->badge(function () {
                    return Producto::where('activo', 1)->count();
                }),
            'Inactivo' =>      Tab::make('Inactivo')
                ->query(fn($query) => $query->where('activo', 0)->orderBy('nombre'))
                ->badge(function () {
                    return Producto::where('activo', 0)->count(); 
                }),
        ];
    }
}
