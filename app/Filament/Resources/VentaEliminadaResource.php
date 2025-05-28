<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaEliminadaResource\Pages;
use App\Filament\Resources\VentaEliminadaResource\RelationManagers;
use App\Models\VentaEliminada;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VentaEliminadaResource extends Resource
{
    protected static ?string $model = VentaEliminada::class;

    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static ?string $navigationGroup = 'Gestión de Ventas';

    public static function getNavigationBadge(): string
    {
        $count = VentaEliminada::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ID_Usuario')
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('Cantidad')
                    ->numeric()
                    ->disabled(),
                Forms\Components\DateTimePicker::make('Fecha_Venta')
                    ->disabled(),
                Forms\Components\TextInput::make('Total')
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('Totalcondescuento')
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('Estado')
                    ->disabled(),
                Forms\Components\TextInput::make('Método_Pago')
                    ->disabled(),
                Forms\Components\TextInput::make('tipo_entrega')
                    ->disabled(),
                Forms\Components\Repeater::make('detalles_venta')
                    ->label('Detalles de la Venta')
                    ->schema([
                        Forms\Components\TextInput::make('id')->label('ID')->disabled(),
                        Forms\Components\TextInput::make('cantidad')->label('Cantidad')->numeric(),
                        Forms\Components\TextInput::make('subtotal')->label('Subtotal')->numeric(),
                        Forms\Components\TextInput::make('venta_id')->label('Venta ID')->disabled(),
                        Forms\Components\TextInput::make('producto_id')->label('Producto ID')->disabled(),
                        Forms\Components\TextInput::make('precio_unitario')->label('Precio Unitario')->numeric(),
                        Forms\Components\DateTimePicker::make('created_at')->label('Fecha Creación')->disabled(),
                        Forms\Components\DateTimePicker::make('updated_at')->label('Última Actualización')->disabled(),
                    ])
                    ->columnSpanFull()
                    ->grid(2)
                    ->disabled()
                    ->collapsed()
                    ->json(),
                Forms\Components\Textarea::make('error_detalle')
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID_Usuario')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Fecha_Venta')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Totalcondescuento')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Estado'),
                Tables\Columns\TextColumn::make('Método_Pago'),
                Tables\Columns\TextColumn::make('tipo_entrega'),
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
            'index' => Pages\ListVentaEliminadas::route('/'),
            'create' => Pages\CreateVentaEliminada::route('/create'),
            'edit' => Pages\EditVentaEliminada::route('/{record}/edit'),
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
