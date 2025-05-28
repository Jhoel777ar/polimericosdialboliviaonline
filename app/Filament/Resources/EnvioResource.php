<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnvioResource\Pages;
use App\Filament\Resources\EnvioResource\RelationManagers;
use App\Models\Envio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Venta;
use App\Models\ComprobantePago;
use App\Models\VentaDetalle;
use Filament\Notifications\Notification;


class EnvioResource extends Resource
{
    protected static ?string $model = Envio::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Gestión de Pagos y Envíos';
    public static function getNavigationBadge(): string
    {
        $count = Envio::count();
        return "{$count}";
    }
    public static function obtenerDetallesVenta($idVenta)
    {
        if (!$idVenta) {
            return [
                'usuario_nombre' => '',
                'usuario_email' => '',
                'productos' => '',
                'estado' => '',
                'total' => '',
                'comprobante_estado' => 'No subió comprobante',
            ];
        }
        $venta = Venta::with(['usuario', 'detalles.producto'])->find($idVenta);
        $comprobante = ComprobantePago::where('ID_Venta', $idVenta)->first();
        if (!$venta) {
            return [
                'usuario_nombre' => 'Venta no encontrada',
                'usuario_email' => '',
                'productos' => '',
                'estado' => '',
                'total' => '',
                'comprobante_estado' => 'No subió comprobante',
            ];
        }
        return [
            'usuario_nombre' => $venta->usuario->name ?? 'Desconocido',
            'usuario_email' => $venta->usuario->email ?? 'Sin correo',
            'productos' => $venta->detalles->pluck('producto.nombre')->implode(', ') ?? 'Sin productos',
            'estado' => $venta->Estado ?? 'Desconocido',
            'total' => number_format($venta->Total, 2) ?? '0.00',
            'comprobante_estado' => $comprobante ? 'Pagado - Listo para envío' : 'No subió comprobante',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ID_Venta')
                    ->label('ID Venta')
                    ->disabled()
                    ->reactive()
                    ->afterStateHydrated(function ($state, $set) {
                        if ($state) {
                            $detalles = self::obtenerDetallesVenta($state);
                            $set('usuario_nombre', $detalles['usuario_nombre']);
                            $set('usuario_email', $detalles['usuario_email']);
                            $set('productos', $detalles['productos']);
                            $set('estado', $detalles['estado']);
                            $set('total', $detalles['total']);
                            $set('comprobante_estado', $detalles['comprobante_estado']);
                        }
                    })
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $detalles = self::obtenerDetallesVenta($state);
                            $set('usuario_nombre', $detalles['usuario_nombre']);
                            $set('usuario_email', $detalles['usuario_email']);
                            $set('productos', $detalles['productos']);
                            $set('estado', $detalles['estado']);
                            $set('total', $detalles['total']);
                            $set('comprobante_estado', $detalles['comprobante_estado']);
                        }
                    }),
                Forms\Components\TextInput::make('comprobante_estado')
                    ->label('Estado del Comprobante de Pago (Verifique)')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('usuario_nombre')
                    ->label('Nombre del Usuario')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('usuario_email')
                    ->label('Email del Usuario')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('productos')
                    ->label('Productos Adquiridos')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('estado')
                    ->label('Estado de la Venta')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('total')
                    ->label('Total de la Venta')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('Empresa_Envio')
                    ->label('Empresa de Envío')
                    ->required()
                    ->maxLength(150)
                    ->disabled(fn($record) => $record && $record->Estado_Envio === 'entregado'),
                Forms\Components\TextInput::make('Numero_Guia')
                    ->label('Número de Guía')
                    ->maxLength(100)
                    ->required()
                    ->disabled(fn($record) => $record && $record->Estado_Envio === 'entregado'),
                Forms\Components\TextInput::make('Dirrecion')
                    ->label('Dirección de Envío')
                    ->disabled(),
                Forms\Components\DatePicker::make('Fecha_Envio')
                    ->label('Fecha de Envío')
                    ->default(now())
                    ->required()
                    ->disabled(fn($record) => $record && $record->Estado_Envio === 'entregado'),
                Forms\Components\Select::make('Estado_Envio')
                    ->label('Estado del Envío')
                    ->options([
                        'pendiente' => 'No se envió',
                        'en tránsito' => 'En tránsito',
                        'entregado' => 'Entregado',
                    ])
                    ->required()
                    ->afterStateHydrated(function ($state) {
                        if ($state === 'entregado') {
                            Notification::make()
                                ->title('Atención')
                                ->body('Este pedido ya fue entregado.En caso de algun problema de envio cambie el estado.')
                                ->warning()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID_Venta')
                    ->label('Venta ID')
                    ->badge()
                    ->color('gray')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('Empresa_Envio')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Numero_Guia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Dirrecion')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('Estado_Envio')
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        if ($state === 'entregado') {
                            return 'success';
                        } elseif ($state === 'en tránsito') {
                            return 'warning';
                        }
                        return 'danger';
                    }),
                Tables\Columns\TextColumn::make('Fecha_Envio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('venta.Total')
                    ->label('Total de la venta')
                    ->sortable(),
                Tables\Columns\TextColumn::make('venta.Cantidad')
                    ->label('Cantidad Total Producto')
                    ->sortable(),
                Tables\Columns\TextColumn::make('venta.usuario.name')
                    ->label('Nombre del Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('venta.usuario.email')
                    ->label('Email del Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('venta.productos')
                    ->label('Productos Aduiridos')
                    ->getStateUsing(function ($record) {
                        return $record->venta->detalles
                            ->map(fn($detalle) => $detalle->producto->nombre)
                            ->join(', ');
                    })
                    ->sortable(false)
                    ->extraAttributes([
                        'class' => 'overflow-x-auto max-w-xs whitespace-nowrap',
                    ]),
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
                Tables\Filters\SelectFilter::make('Estado_Envio')
                    ->label('Estado del Envío')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en tránsito' => 'En tránsito',
                        'entregado' => 'Entregado',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['Estado_Envio'])) {
                            return $query->where('Estado_Envio', $data['Estado_Envio']);
                        }
                        return $query;
                    }),

                Tables\Filters\Filter::make('Fecha_Envio')
                    ->label('Rango de Fechas de Envío')
                    ->form([
                        Forms\Components\DateTimePicker::make('start')
                            ->label('Fecha Inicio')
                            ->placeholder('Seleccione una fecha de inicio'),

                        Forms\Components\DateTimePicker::make('end')
                            ->label('Fecha Fin')
                            ->placeholder('Seleccione una fecha de fin'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['start']) && isset($data['end'])) {
                            return $query->whereBetween('Fecha_Envio', [$data['start'], $data['end']]);
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
            'index' => Pages\ListEnvios::route('/'),
            'create' => Pages\CreateEnvio::route('/create'),
            'edit' => Pages\EditEnvio::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
}
