<?php

namespace App\Filament\Resources\VentaEliminadaResource\Pages;

use App\Filament\Resources\VentaEliminadaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVentaEliminada extends EditRecord
{
    protected static string $resource = VentaEliminadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
