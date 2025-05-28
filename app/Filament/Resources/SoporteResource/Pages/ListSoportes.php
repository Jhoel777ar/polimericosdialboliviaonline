<?php

namespace App\Filament\Resources\SoporteResource\Pages;

use App\Filament\Resources\SoporteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Soporte;

class ListSoportes extends ListRecords
{
    protected static string $resource = SoporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'Total de Consultas' => Tab::make()
                ->query(fn($query) => $query->orderBy('consultation_date'))
                ->badge(function () {
                    return Soporte::count();
                }),
            'Pendientes' => Tab::make()
                ->query(fn($query) => $query->where('status', 'pending')->orderBy('consultation_date'))
                ->badge(function () {
                    return Soporte::where('status', 'pending')->count();
                }),
            'Resueltos' => Tab::make()
                ->query(fn($query) => $query->where('status', 'resolved')->orderBy('consultation_date'))
                ->badge(function () {
                    return Soporte::where('status', 'resolved')->count();
                }),
        ];
    }
}
