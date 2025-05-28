<?php

namespace App\Filament\Resources\ProveedorResource\Pages;

use App\Filament\Resources\ProveedorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Proveedor;

class ListProveedors extends ListRecords
{
    protected static string $resource = ProveedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make()
                ->query(fn($query) => $query->orderBy('nombre'))
                ->badge(function () {
                    return Proveedor::count();
                }),
            'Con Correo' => Tab::make()
                ->query(fn($query) => $query->whereNotNull('correo')->orderBy('nombre'))
                ->badge(function () {
                    return Proveedor::whereNotNull('correo')->count();
                }),
        ];
    }
}
