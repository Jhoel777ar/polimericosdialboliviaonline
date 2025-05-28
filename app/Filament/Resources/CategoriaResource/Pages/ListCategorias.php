<?php

namespace App\Filament\Resources\CategoriaResource\Pages;

use App\Filament\Resources\CategoriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Categoria;

class ListCategorias extends ListRecords
{
    protected static string $resource = CategoriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->query(fn($query) => $query->orderBy('nombre'))
                ->badge(function () {
                    return Categoria::count();
                }),
        ];
    }
}
