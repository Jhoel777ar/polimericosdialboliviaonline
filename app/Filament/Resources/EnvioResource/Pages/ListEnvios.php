<?php

namespace App\Filament\Resources\EnvioResource\Pages;

use App\Filament\Resources\EnvioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Envio;

class ListEnvios extends ListRecords
{
    protected static string $resource = EnvioResource::class;

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
                ->query(fn($query) => $query->orderBy('created_at', 'desc'))
                ->badge(function () {
                    return Envio::count();
                }),
            'Pendiente' => Tab::make()
                ->query(fn($query) => $query->where('Estado_Envio', 'pendiente')->orderBy('created_at', 'desc'))
                ->badge(function () {
                    return Envio::where('Estado_Envio', 'pendiente')->count();
                }) -> badgeColor('danger'),
            'En tránsito' => Tab::make()
                ->query(fn($query) => $query->where('Estado_Envio', 'en tránsito')->orderBy('created_at', 'desc'))
                ->badge(function () {
                    return Envio::where('Estado_Envio', 'en tránsito')->count();
                }),
            'Entregado' => Tab::make()
                ->query(fn($query) => $query->where('Estado_Envio', 'entregado')->orderBy('created_at', 'desc'))
                ->badge(function () {
                    return Envio::where('Estado_Envio', 'entregado')->count();
                }) -> badgeColor('success'),
        ];
    }
}
