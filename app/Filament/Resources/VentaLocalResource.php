<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaLocalResource\Pages;
use App\Filament\Resources\VentaLocalResource\RelationManagers;
use App\Models\VentaLocal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VentaLocalResource extends Resource
{
    protected static ?string $model = VentaLocal::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Gestión de Ventas';

    public static function getNavigationBadge(): string
    {
        $count = VentaLocal::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre_usuario')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\TextInput::make('telefono')
                    ->tel()
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\TextInput::make('CI')
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\TextInput::make('cantidad')
                    ->required()
                    ->numeric()
                    ->disabled(),
                Forms\Components\DateTimePicker::make('fecha_venta')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('estado')
                    ->required()
                    ->disabled(),
                Forms\Components\Repeater::make('detalle_compra')
                    ->label('Detalles de la Compra')
                    ->schema([
                        Forms\Components\TextInput::make('ID')
                            ->label('ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('Producto')
                            ->label('Producto')
                            ->disabled(),
                        Forms\Components\TextInput::make('Color')
                            ->label('Color')
                            ->disabled(),
                        Forms\Components\TextInput::make('Cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('Precio')
                            ->label('Precio')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('Categoría')
                            ->label('Categoría')
                            ->disabled(),
                        Forms\Components\TextInput::make('Precio Proveedor')
                            ->label('Precio Proveedor')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('Precio Estudiante')
                            ->label('Precio Estudiante')
                            ->numeric()
                            ->disabled(),
                    ])
                    ->columnSpanFull()
                    ->grid(2)
                    ->disabled()
                    ->collapsed()
                    ->json(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_venta')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado'),
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
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVentaLocals::route('/'),
            'create' => Pages\CreateVentaLocal::route('/create'),
            'edit' => Pages\EditVentaLocal::route('/{record}/edit'),
        ];
    }
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
}
