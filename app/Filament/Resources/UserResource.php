<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administrar Usuarios';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): string
    {
        $count = User::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('adminArk')
                    ->label('Roles')
                    ->options([
                        'adminArk' => 'AdminArk',
                        'Cliente' => 'Cliente',
                    ])
                    ->default('Cliente')
                    ->required(),
                Forms\Components\Select::make('estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->default(1)
                    ->required(),
                Forms\Components\TextInput::make('ci')
                    ->label('CI')
                    ->required()
                    ->maxLength(15)
                    ->unique('users', 'ci'),
                Forms\Components\TextInput::make('telefono')
                    ->label('Teléfono')
                    ->required()
                    ->maxLength(15)
                    ->numeric(),
                //Forms\Components\Textarea::make('two_factor_secret')
                //    ->columnSpanFull(),
                //Forms\Components\Textarea::make('two_factor_recovery_codes')
                //    ->columnSpanFull(),
                //Forms\Components\DateTimePicker::make('two_factor_confirmed_at'),
                //Forms\Components\TextInput::make('current_team_id')
                //    ->numeric(),
                //Forms\Components\TextInput::make('profile_photo_path')
                //    ->maxLength(2048),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                //Tables\Columns\TextColumn::make('two_factor_confirmed_at')
                //    ->dateTime(),
                //Tables\Columns\TextColumn::make('current_team_id')
                //    ->numeric(),
                Tables\Columns\TextColumn::make('adminArk')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('estado')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state == 1 ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Activo' : 'Inactivo'),
                Tables\Columns\TextColumn::make('ci')
                    ->sortable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('profile_photo_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->placeholder('Filter by name'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['name'] ?? null, fn($query, $name) => $query->where('name', 'like', "%{$name}%"))),

                Tables\Filters\Filter::make('email')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Filter by email'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['email'] ?? null, fn($query, $email) => $query->where('email', 'like', "%{$email}%"))),

                Tables\Filters\Filter::make('adminArk')
                    ->form([
                        Forms\Components\Select::make('adminArk')
                            ->label('Role')
                            ->options([
                                'adminArk' => 'AdminArk',
                                'Cliente' => 'Cliente',
                            ])
                            ->placeholder('Filter by role'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['adminArk'] ?? null, fn($query, $role) => $query->where('adminArk', $role))),
                Tables\Filters\Filter::make('ci')
                    ->form([
                        Forms\Components\TextInput::make('ci')
                            ->label('CI')
                            ->placeholder('Filter by CI'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query->when($data['ci'] ?? null, fn($query, $ci) => $query->where('ci', 'like', "%{$ci}%"))),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
