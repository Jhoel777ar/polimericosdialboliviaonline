<?php

namespace App\Filament\Resources\ComprobantePagoResource\Pages;

use App\Filament\Resources\ComprobantePagoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\ComprobantePago;
use Filament\Resources\Components\Tab;

class ListComprobantePagos extends ListRecords
{
    protected static string $resource = ComprobantePagoResource::class;

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
                ->badge(fn() => ComprobantePago::count()),
            'Pendientes' => Tab::make()
                ->query(fn($query) => $query->where('estado', 'pendiente')->orderBy('created_at', 'desc'))
                ->badge(fn() => ComprobantePago::where('estado', 'pendiente')->count())
                ->badgeColor('danger'),
            'Aprobados' => Tab::make()
                ->query(fn($query) => $query->where('estado', 'aprobado')->orderBy('created_at', 'desc'))
                ->badge(fn() => ComprobantePago::where('estado', 'aprobado')->count())
                ->badgeColor('success'),
            'Rechazados' => Tab::make()
                ->query(fn($query) => $query->where('estado', 'rechazado')->orderBy('created_at', 'desc'))
                ->badge(fn() => ComprobantePago::where('estado', 'rechazado')->count())
                ->badgeColor('warning'),
        ];
    }
}
