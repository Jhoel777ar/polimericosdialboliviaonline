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
                    ->options(\App\Models\Categoria::query()->pluck('nombre', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Seleccione una categoría'),
                Forms\Components\TextInput::make('precio')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('precio_estudiante')
                    ->label('Precio Estudiantes (Opcional)')
                    ->numeric(),
                Forms\Components\TextInput::make('precio_proveedor')
                    ->label('Precio Proveedores (Opcional)')
                    ->numeric(),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(1),
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
                    ->options(\App\Models\Proveedor::query()->pluck('nombre', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Seleccione un proveedor'),
                Forms\Components\Select::make('color_id')
                    ->label('Color')
                    ->options(\App\Models\Color::query()->pluck('color', 'id'))
                    ->searchable()
                    ->placeholder('Seleccione un color'),
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
