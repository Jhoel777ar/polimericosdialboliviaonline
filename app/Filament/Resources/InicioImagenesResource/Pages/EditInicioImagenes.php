<?php

namespace App\Filament\Resources\InicioImagenesResource\Pages;

use App\Filament\Resources\InicioImagenesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInicioImagenes extends EditRecord
{
    protected static string $resource = InicioImagenesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
