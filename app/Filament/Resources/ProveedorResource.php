<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Filament\Resources\ProveedorResource\RelationManagers;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Gestión de Inventario';

    public static function getNavigationBadge(): string
    {
        $count = Proveedor::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('telefono')
                    ->tel()
                    ->maxLength(20)
                    ->required(),
                Forms\Components\TextInput::make('correo')
                    ->maxLength(150)
                    ->email()
                    ->required(),
                Forms\Components\Textarea::make('direccion')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('correo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('nombre')
                    ->form([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->placeholder('Filtrar por nombre')
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['nombre'])) {
                            $query->where('nombre', 'like', '%' . $data['nombre'] . '%');
                        }
                    }),

                Tables\Filters\Filter::make('telefono')
                    ->form([
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->placeholder('Filtrar por teléfono')
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['telefono'])) {
                            $query->where('telefono', 'like', '%' . $data['telefono'] . '%');
                        }
                    }),
                Tables\Filters\Filter::make('correo')
                    ->form([
                        Forms\Components\TextInput::make('correo')
                            ->label('Correo')
                            ->placeholder('Filtrar por correo')
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['correo'])) {
                            $query->where('correo', 'like', '%' . $data['correo'] . '%');
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
