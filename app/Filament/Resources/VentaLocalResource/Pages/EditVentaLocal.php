<?php

namespace App\Filament\Resources\VentaLocalResource\Pages;

use App\Filament\Resources\VentaLocalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVentaLocal extends EditRecord
{
    protected static string $resource = VentaLocalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
