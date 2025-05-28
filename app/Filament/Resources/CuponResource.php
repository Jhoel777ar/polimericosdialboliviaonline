<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuponResource\Pages;
use App\Filament\Resources\CuponResource\RelationManagers;
use App\Models\Cupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CuponResource extends Resource
{
    protected static ?string $model = Cupon::class;
    protected static ?string $navigationGroup = 'Descuentos';
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    public static function getNavigationBadge(): string
    {
        $count = Cupon::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('descuento')
                    ->label('Aplique descuento en Bs.')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $set('porcentaje', null);
                        }
                    }),
                Forms\Components\TextInput::make('porcentaje')
                    ->label('Aplique descuento en %')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $set('descuento', null);
                        }
                    }),
                Forms\Components\TextInput::make('uso_maximo')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\DateTimePicker::make('fecha_expiracion'),
                Forms\Components\Toggle::make('activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('descuento')
                    ->label('Descuento en Bs.')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('porcentaje')
                    ->label('Descuento en %')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('uso_maximo')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('usos_actuales')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('fecha_expiracion')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
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
            'index' => Pages\ListCupons::route('/'),
            'create' => Pages\CreateCupon::route('/create'),
            'edit' => Pages\EditCupon::route('/{record}/edit'),
        ];
    }
}
