<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Resources\Components\Tab;
use App\Models\User;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
                ->query(fn($query) => $query->orderBy('name'))
                ->badge(function () {
                    return User::count();
                }),
            'AdminArk' => Tab::make()
                ->query(fn($query) => $query->where('adminArk', 'adminArk')->orderBy('name'))
                ->badge(function () {
                    return User::where('adminArk', 'adminArk')->count();
                }),
            'Cliente' => Tab::make()
                ->query(fn($query) => $query->where('adminArk', 'Cliente')->orderBy('name'))
                ->badge(function () {
                    return User::where('adminArk', 'Cliente')->count();
                }),
        ];
    }
}
