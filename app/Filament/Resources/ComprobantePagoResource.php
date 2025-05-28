<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComprobantePagoResource\Pages;
use App\Filament\Resources\ComprobantePagoResource\RelationManagers;
use App\Models\ComprobantePago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use App\Models\Venta;

class ComprobantePagoResource extends Resource
{
    protected static ?string $model = ComprobantePago::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Gestión de Pagos y Envíos';

    public static function getNavigationBadge(): string
    {
        $count = ComprobantePago::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ID_Venta')
                    ->label('Venta ID')
                    ->disabled(),
                Forms\Components\FileUpload::make('image_url')
                    ->image()
                    ->dehydrated(false)
                    ->disabled()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->downloadable()
                    ->openable()
                    ->fetchFileInformation(false)
                    ->disk('public')
                    ->directory('comprobantes_pago')
                    ->afterStateUpdated(function ($state, $get) {
                        if (!$state) {
                            $comprobantePago = ComprobantePago::find($get('id'));
                            if ($comprobantePago && $comprobantePago->image_url) {
                                Storage::disk('public')->delete($comprobantePago->image_url);
                            }
                        }
                    }),
                Forms\Components\DateTimePicker::make('Fecha_Subida')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('monto_reportado')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('description')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('subido')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->required()
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ])
                    ->default('pendiente'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ID_Venta')
                    ->label('Venta ID')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_comprobantes')
                    ->label('Cantidad de Comprobantes')
                    ->getStateUsing(function ($record) {
                        return ComprobantePago::where('ID_Venta', $record->ID_Venta)->count();
                    })
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('venta.usuario.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('venta.usuario.email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('venta.Total')
                    ->label('Total')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($state == $record->monto_reportado) {
                            return 'success';
                        } elseif ($state < $record->monto_reportado) {
                            return 'danger';
                        }
                        return 'warning';
                    }),

                Tables\Columns\TextColumn::make('monto_reportado')
                    ->label('Monto Reportado')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($state == $record->venta->Total) {
                            return 'success';
                        } elseif ($state < $record->venta->Total) {
                            return 'danger';
                        }
                        return 'warning';
                    }),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(function ($state) {
                        if ($state === 'aprobado') {
                            return 'success';
                        } elseif ($state === 'rechazado') {
                            return 'danger';
                        } elseif ($state === 'pendiente') {
                            return 'warning';
                        }
                        return 'gray';
                    }),
                Tables\Columns\TextColumn::make('subido')
                    ->label('Subido Comprobante')
                    ->badge()
                    ->color(function ($state) {
                        if ($state === 1) {
                            return 'success';
                        }
                        return 'danger';
                    }),
                Tables\Columns\TextColumn::make('venta.Fecha_Venta')
                    ->label('Fecha de Venta')
                    ->dateTime(),
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Imagen')
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('Fecha_Subida')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListComprobantePagos::route('/'),
            'create' => Pages\CreateComprobantePago::route('/create'),
            'edit' => Pages\EditComprobantePago::route('/{record}/edit'),
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
}
