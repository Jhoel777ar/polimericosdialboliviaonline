<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarritoResource\Pages;
use App\Filament\Resources\CarritoResource\RelationManagers;
use App\Models\Carrito;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarritoResource extends Resource
{
    protected static ?string $model = Carrito::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Gestión de Ventas';

    public static function getNavigationBadge(): string
    {
        $count = Carrito::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.id')
                    ->label('Id Usuario')
                    ->badge()
                    ->color(fn($record) => \App\Models\Carrito::where('ID_Usuario', $record->usuario->id)->count() > 1 ? 'gray' : 'info'),
                Tables\Columns\TextColumn::make('usuario_count')
                    ->label('Cantidad de Carritos')
                    ->getStateUsing(fn($record) => \App\Models\Carrito::where('ID_Usuario', $record->usuario->id)->count()),
                Tables\Columns\TextColumn::make('usuario.name')->label('Usuario'),
                Tables\Columns\TextColumn::make('usuario.email')->label('Email'),
                Tables\Columns\TextColumn::make('producto.nombre')->label('Producto'),
                Tables\Columns\TextColumn::make('producto.color.color')
                    ->label('Color')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('producto.categoria.nombre')
                    ->label('Categoría')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('Cantidad')
                    ->numeric()
                    ->badge()
                    ->color("info"),
                Tables\Columns\TextColumn::make('Fecha_Agregado')
                    ->dateTime()
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
                Tables\Filters\SelectFilter::make('ID_Usuario')
                    ->label('Usuario')
                    ->options(function () {
                        return \App\Models\User::all()->pluck('name', 'id')->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['ID_Usuario'])) {
                            return $query->where('ID_Usuario', $data['ID_Usuario']);
                        }
                        return $query;
                    }),
                Tables\Filters\SelectFilter::make('ID_Producto')
                    ->label('Producto')
                    ->options(function () {
                        return \App\Models\Producto::where('stock', '>', 0)->pluck('nombre', 'id')->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['ID_Producto'])) {
                            return $query->where('ID_Producto', $data['ID_Producto']);
                        }
                        return $query;
                    }),
                Tables\Filters\Filter::make('Fecha_Agregado')
                    ->label('Rango de Fechas')
                    ->form([
                        Forms\Components\DateTimePicker::make('start')
                            ->label('Fecha Inicio')
                            ->placeholder('Seleccionar fecha de inicio'),

                        Forms\Components\DateTimePicker::make('end')
                            ->label('Fecha Fin')
                            ->placeholder('Seleccionar fecha de fin'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['start']) && !empty($data['end'])) {
                            return $query->whereBetween('Fecha_Agregado', [
                                $data['start'],
                                $data['end'],
                            ]);
                        }
                        return $query;
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
            'index' => Pages\ListCarritos::route('/'),
            'create' => Pages\CreateCarrito::route('/create'),
            'edit' => Pages\EditCarrito::route('/{record}/edit'),
        ];
    }
}
