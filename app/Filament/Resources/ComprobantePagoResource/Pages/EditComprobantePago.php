<?php

namespace App\Filament\Resources\ComprobantePagoResource\Pages;

use App\Filament\Resources\ComprobantePagoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComprobantePago extends EditRecord
{
    protected static string $resource = ComprobantePagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
