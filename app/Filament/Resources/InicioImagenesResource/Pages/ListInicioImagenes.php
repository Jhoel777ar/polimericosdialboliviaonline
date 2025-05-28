<?php

namespace App\Filament\Resources\InicioImagenesResource\Pages;

use App\Filament\Resources\InicioImagenesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInicioImagenes extends ListRecords
{
    protected static string $resource = InicioImagenesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
