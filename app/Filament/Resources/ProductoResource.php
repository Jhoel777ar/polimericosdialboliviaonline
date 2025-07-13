<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Actions\Action;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-plus';

    protected static ?string $navigationGroup = 'Gestión de Inventario';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function getNavigationBadge(): string
    {
        $count = Producto::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(150),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull()
                    ->maxLength(500),
                Forms\Components\Select::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre') 
                    ->searchable()
                    ->required()
                    ->placeholder('Seleccione una categoría')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre de la categoría')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->maxLength(500),

                        Forms\Components\FileUpload::make('imagen_url')
                            ->label('Imagen de categoría')
                            ->image()
                            ->directory('categorias')
                            ->disk('public')
                            ->openable(),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action
                            ->modalHeading('Crear nueva categoría')
                            ->modalSubmitActionLabel('Crear categoría')
                            ->modalWidth('lg');
                    }),
                Forms\Components\TextInput::make('precio')
                    ->required()
                    ->numeric()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('Bs')
                    ->default(1),
                Forms\Components\TextInput::make('precio_estudiante')
                    ->label('Precio Estudiantes (Opcional)')
                    ->required()
                    ->numeric()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('Bs')
                    ->default(1),
                Forms\Components\TextInput::make('precio_proveedor')
                    ->label('Precio Proveedores (Opcional)')
                    ->required()
                    ->numeric()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('Bs')
                    ->default(1),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->suffix('Unidades'),
                Forms\Components\FileUpload::make('imagen_url')
                    ->image()
                    ->imageEditor()
                    ->maxParallelUploads(1)
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
                    ->directory('productos')
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if (!$state && $get('id')) {
                            $producto = Producto::find($get('id'));
                            if ($producto && $producto->imagen_url) {
                                Storage::disk('public')->delete($producto->imagen_url);
                            }
                        }
                    }),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'disponible' => 'Disponible',
                        'descontinuado' => 'Descontinuado',
                    ])
                    ->default('disponible')
                    ->required(),
                Forms\Components\Select::make('proveedor_id')
                    ->label('Proveedor')
                    ->relationship('proveedor', 'nombre')
                    ->searchable()
                    ->required()
                    ->placeholder('Seleccione un proveedor')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre del proveedor')
                            ->required()
                            ->maxLength(150),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('correo')
                            ->label('Correo electrónico')
                            ->email()
                            ->maxLength(150),

                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección')
                            ->maxLength(500),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action
                            ->modalHeading('Crear nuevo proveedor')
                            ->modalSubmitActionLabel('Crear proveedor')
                            ->modalWidth('lg');
                    }),
                Forms\Components\Select::make('color_id')
                    ->label('Color')
                    ->relationship('color', 'color')
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn(string $search) =>
                        \App\Models\Color::query()
                            ->where('color', 'like', "%{$search}%")
                            ->pluck('color', 'id')
                            ->toArray()
                    )
                    ->getOptionLabelUsing(
                        fn($value) =>
                        \App\Models\Color::find($value)?->color
                    )
                    ->placeholder('Seleccione un color')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('color')
                            ->label('Nombre del color')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action
                            ->modalHeading('Crear nuevo color')
                            ->modalSubmitActionLabel('Crear color')
                            ->modalWidth('lg');
                    }),
                Forms\Components\Select::make('activo')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->default(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Producto ID')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('precio')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_estudiante')
                    ->label('Precio Estudiantes')
                    ->numeric()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('precio_proveedor')
                    ->label('Precio proveedores')
                    ->numeric()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->badge()
                    ->sortable()
                    ->color(function ($state) {
                        return $state === 0 ? 'danger' : 'success';
                    }),
                Tables\Columns\TextColumn::make('activo')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state == 1 ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Activo' : 'Inactivo'),
                Tables\Columns\ImageColumn::make('imagen_url')
                    ->label('Imagen')
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('color.color')
                    ->label('Color')
                    ->sortable(),
                //Tables\Columns\TextColumn::make('descripcion'),
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
                Tables\Filters\SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->options(\App\Models\Categoria::query()->pluck('nombre', 'id')),
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'disponible' => 'Disponible',
                        'descontinuado' => 'Descontinuado',
                    ]),
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
