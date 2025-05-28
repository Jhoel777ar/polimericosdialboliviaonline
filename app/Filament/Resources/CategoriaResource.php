<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaResource\Pages;
use App\Filament\Resources\CategoriaResource\RelationManagers;
use App\Models\Categoria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Gestión de Inventario';

    public static function getNavigationBadge(): string
    {
        $count = Categoria::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('imagen_url')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->downloadable()
                    ->required()
                    ->openable()
                    ->fetchFileInformation(false)
                    ->disk('public')
                    ->directory('categoria')
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if (!$state && $get('id')) {
                            $categoria = Categoria::find($get('id'));
                            if ($categoria && $categoria->imagen_url) {
                                Storage::disk('public')->delete($categoria->imagen_url);
                            }
                        }
                    }),
                Forms\Components\Select::make('activo')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->default(1)
                    ->required(),
                Forms\Components\Select::make('parent_id')
                    ->label('Subcategoría de')
                    ->options(Categoria::pluck('nombre', 'id'))
                    ->searchable()
                    ->nullable()
                    ->helperText('Selecciona una categoría padre si aplica'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('imagen_url')
                    ->label('Imagen')
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('parent_id')
                    ->label('Categoría Padre')
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return 'Sin categoría padre';
                        }
                        $parent = Categoria::find($state);
                        return $parent ? $parent->nombre : 'Categoría no encontrada';
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('activo')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state == 1 ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Activo' : 'Inactivo'),
                Tables\Columns\TextColumn::make('descripcion')
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
                Tables\Filters\Filter::make('descripcion')
                    ->form([
                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Filtrar por descripción')
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['descripcion'])) {
                            $query->where('descripcion', 'like', '%' . $data['descripcion'] . '%');
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
            'index' => Pages\ListCategorias::route('/'),
            'create' => Pages\CreateCategoria::route('/create'),
            'edit' => Pages\EditCategoria::route('/{record}/edit'),
        ];
    }
}
