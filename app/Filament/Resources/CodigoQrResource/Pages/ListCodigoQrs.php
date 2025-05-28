<?php

namespace App\Filament\Resources\CodigoQrResource\Pages;

use App\Filament\Resources\CodigoQrResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCodigoQrs extends ListRecords
{
    protected static string $resource = CodigoQrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
