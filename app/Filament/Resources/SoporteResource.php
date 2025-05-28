<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoporteResource\Pages;
use App\Filament\Resources\SoporteResource\RelationManagers;
use App\Models\Soporte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SoporteResource extends Resource
{
    protected static ?string $model = Soporte::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Consultas y preguntas ';

    public static function getNavigationBadge(): string
    {
        $count = Soporte::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
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
                    ->getSearchResultsUsing(function ($search) {
                        return \App\Models\User::query()
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orderBy('name')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($user) {
                                return [
                                    $user->id => "{$user->name} ({$user->email})",
                                ];
                            });
                    })
                    ->required()
                    ->placeholder('Seleccione un usuario')
                    ->afterStateUpdated(function ($state) {}),
                //Forms\Components\DateTimePicker::make('consultation_date')
                //    ->label('Fecha de consulta')
                //    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->label('Asunto')
                    ->required()
                    ->maxLength(150),
                Forms\Components\Textarea::make('description')
                ->label('Pregunta del usuario')
                ->disabled() 
                ->columnSpanFull(),
                Forms\Components\TextInput::make('respuesta')
                    ->label('Respuesta')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'resolved' => 'Resuelto',
                    ])
                    ->default('resolved')
                    ->required()
                    ->placeholder('Seleccione un estado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Tables\Columns\TextColumn::make('user_id')
                //    ->numeric()
                //    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('consultation_date')
                    ->label('Fecha de consulta')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Asunto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(
                        fn($state) =>
                        $state == 'pending' ? 'danger' : ($state == 'resolved' ? 'success' : 'secondary')
                    )
                    ->formatStateUsing(
                        fn($state) =>
                        $state == 'pending' ? 'Pendiente' : ($state == 'resolved' ? 'Resuelto' : 'Desconocido')
                    ),
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
            'index' => Pages\ListSoportes::route('/'),
            'create' => Pages\CreateSoporte::route('/create'),
            'edit' => Pages\EditSoporte::route('/{record}/edit'),
        ];
    }
}
