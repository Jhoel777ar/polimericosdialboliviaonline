<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InicioImagenesResource\Pages;
use App\Filament\Resources\InicioImagenesResource\RelationManagers;
use App\Models\InicioImagenes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class InicioImagenesResource extends Resource
{
    protected static ?string $model = InicioImagenes::class;
    protected static ?string $navigationGroup = 'Carrusel principal de la página';
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function getNavigationBadge(): string
    {
        $count = InicioImagenes::count();
        return "{$count}";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_url')
                    ->image()
                    ->required()
                    ->maxParallelUploads(1)
                    ->imageEditor()
                    ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
                    ->downloadable()
                    ->openable()
                    ->fetchFileInformation(false)
                    ->disk('public')
                    ->directory('imagenes_inicio')
                    ->afterStateUpdated(function ($state, $get, $set) {
                        if (\App\Models\InicioImagenes::count() >= 12) {
                            Notification::make()
                                ->title('Límite alcanzado')
                                ->body('No puedes agregar más de 12 imágenes.')
                                ->danger()
                                ->send();
                            $set('image_url', null);
                        } else {
                            $record = $get('id') ? \App\Models\InicioImagenes::find($get('id')) : null;
                            if ($record && $record->image_url !== $state) {
                                Storage::disk('public')->delete($record->image_url);
                            }
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url'),
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
            'index' => Pages\ListInicioImagenes::route('/'),
            'create' => Pages\CreateInicioImagenes::route('/create'),
            'edit' => Pages\EditInicioImagenes::route('/{record}/edit'),
        ];
    }
}
