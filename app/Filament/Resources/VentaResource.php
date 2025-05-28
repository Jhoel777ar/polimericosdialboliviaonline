<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Gestión de Ventas';

    public static function getNavigationBadge(): string
    {
        $count = Venta::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->label('Generar ventas de un producto en cantidad grande')
                    ->disabled()
                    ->helperText('Este formulario permite generar ventas de un solo producto por venta. Si desea vender grandes cantidades de un producto, puede ajustar la cantidad en el campo correspondiente. Asegúrese de que el stock del producto sea suficiente para completar la venta.')
                    ->extraAttributes(['class' => 'text-lg font-semibold mb-4']),
                Forms\Components\Select::make('ID_Usuario')
                    ->label('Usuario')
                    ->options(function () {
                        return \App\Models\User::query()
                            ->orderBy('name')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($user) {
                                return [
                                    $user->id => "{$user->name} ({$user->email})",
                                ];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->placeholder('Seleccione un usuario'),

                Forms\Components\Select::make('ID_Producto')
                    ->label('Producto')
                    ->options(function () {
                        return \App\Models\Producto::query()
                            ->where('stock', '>', 0)
                            ->get()
                            ->mapWithKeys(function ($producto) {
                                return [
                                    $producto->id => "{$producto->nombre} (Stock: {$producto->stock})",  // Mostrar nombre y stock
                                ];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->placeholder('Seleccione un producto')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $producto = \App\Models\Producto::find($state);
                        if ($producto) {
                            $set('Precio_Unitario', $producto->precio);
                            $cantidad = $get('Cantidad');
                            if ($cantidad > $producto->stock) {
                                $set('Cantidad', $producto->stock);
                                $set('warning', "Stock insuficiente. Se ajustará la cantidad a {$producto->stock}.");
                            } else {
                                $set('warning', null);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('Cantidad')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->minValue(0)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $producto = \App\Models\Producto::find($get('ID_Producto'));
                        if ($producto) {
                            if ($state < 1) {
                                $state = 1;
                                $set('Cantidad', $state);
                            }
                            if ($state > $producto->stock) {
                                $state = $producto->stock;
                                $set('Cantidad', $state);
                            }
                            $total = $producto->precio * $state;
                            $total = number_format($total, 2, '.', '');
                            $set('Total', $total);
                        }
                    })
                    ->default(0),

                Forms\Components\TextInput::make('warning')
                    ->disabled()
                    ->helperText(function ($get) {
                        return $get('warning');
                    }),

                Forms\Components\TextInput::make('Precio_Unitario')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(function ($get) {
                        $producto = \App\Models\Producto::find($get('ID_Producto'));
                        return $producto ? $producto->precio : 0;
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $cantidad = $get('Cantidad');
                        $total = $state * $cantidad;
                        $set('Total', $total);
                    }),
                Forms\Components\TextInput::make('Total')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                        'enviado' => 'Enviado',
                        'entregado' => 'Entregado',
                    ])
                    ->required(),
                Forms\Components\Select::make('Método_Pago')
                    ->options([
                        'tarjeta' => 'Tarjeta',
                        'paypal' => 'PayPal',
                        'transferencia' => 'Transferencia',
                        'efectivo' => 'Efectivo',
                    ])
                    ->required(),
                Forms\Components\Select::make('tipo_entrega')
                    ->options([
                        'local' => 'Local',
                    ])
                    ->default('local')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Venta ID')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuario.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuario.email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Cantidad')
                    ->numeric()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('Fecha_Venta')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Total')
                    ->numeric()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('Totalcondescuento')
                    ->numeric()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('Estado')
                    ->badge()
                    ->color(function ($state) {
                        if ($state == 'pagado') {
                            return 'danger';
                        } elseif ($state == 'enviado') {
                            return 'warning';
                        } elseif ($state == 'entregado') {
                            return 'success';
                        }
                        return 'danger';
                    }),
                Tables\Columns\TextColumn::make('Método_Pago')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('tipo_entrega')
                    ->searchable()
                    ->badge()
                    ->color('info'),
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
                Tables\Filters\SelectFilter::make('Estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                        'enviado' => 'Enviado',
                        'entregado' => 'Entregado',
                    ]),
                Tables\Filters\SelectFilter::make('Método_Pago')
                    ->label('Método de Pago')
                    ->options([
                        'tarjeta' => 'Tarjeta',
                        'paypal' => 'PayPal',
                        'transferencia' => 'Transferencia',
                        'efectivo' => 'Efectivo',
                    ]),
                Filter::make('Fecha_Venta')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['fecha_inicio'], fn($q) => $q->where('Fecha_Venta', '>=', $data['fecha_inicio']))
                            ->when($data['fecha_fin'], fn($q) => $q->where('Fecha_Venta', '<=', $data['fecha_fin']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('generarFactura')
                    ->label('Generar Factura')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->url(fn(Venta $venta) => route('ventas.generarFactura', $venta->id))
                    ->openUrlInNewTab(),

                Action::make('limpiarVentasAntiguas')
                    ->label('Eliminar Ventas Antiguas')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn() => self::eliminarVentasAntiguas())
                    ->color('danger'),
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
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }

    public static function eliminarVentasAntiguas()
    {
        $fechaLimite = now()->subWeek();
        
        $ventasAEliminar = Venta::where('Estado', 'cancelado')
            ->orWhere(function ($query) use ($fechaLimite) {
                $query->where('Fecha_Venta', '<', $fechaLimite)
                    ->whereNotExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('comprobante_pagos')
                            ->whereColumn('comprobante_pagos.ID_Venta', 'ventas.id');
                    });
            })
            ->get();

        if ($ventasAEliminar->isEmpty()) {
            Notification::make()
                ->title('No se encontraron ventas para eliminar')
                ->body('No hay ventas que cumplan con los criterios de eliminación.')
                ->success()
                ->send();
            return;
        }

        $ventasEliminadas = $ventasAEliminar->pluck('id')->toArray();
        Venta::whereIn('id', $ventasEliminadas)->delete();

        Notification::make()
            ->title('Ventas eliminadas correctamente')
            ->body('Se eliminaron ' . count($ventasEliminadas) . ' ventas.')
            ->success()
            ->send();
    }
}
