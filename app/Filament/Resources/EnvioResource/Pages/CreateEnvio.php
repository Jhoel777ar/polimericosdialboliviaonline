<?php

namespace App\Filament\Resources\EnvioResource\Pages;

use App\Filament\Resources\EnvioResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEnvio extends CreateRecord
{
    protected static string $resource = EnvioResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        abort(403, 'La creación de nuevos envíos está deshabilitada.');
    }
}
