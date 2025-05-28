<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CodigoQrResource\Pages;
use App\Filament\Resources\CodigoQrResource\RelationManagers;
use App\Models\CodigoQr;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class CodigoQrResource extends Resource
{
    protected static ?string $model = CodigoQr::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    public static function getNavigationBadge(): string
    {
        $count = CodigoQR::count();
        return "{$count}";
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('qr_image_url')
                    ->image()
                    ->required()
                    ->imageEditor()
                    ->maxParallelUploads(1)
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->downloadable()
                    ->disk('public')
                    ->directory('Pagosqr')
                    ->downloadable()
                    ->openable()
                    ->fetchFileInformation(false)
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $record = $get('id') ? \App\Models\CodigoQr::find($get('id')) : null;
                        if ($record && $record->qr_image_url !== $state) {
                            Storage::disk('public')->delete($record->qr_image_url);
                        }
                    }),
                Forms\Components\TextInput::make('monto_aceptado')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('fecha_expiracion'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('qr_image_url'),
                Tables\Columns\TextColumn::make('monto_aceptado')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('fecha_expiracion')
                    ->dateTime()
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
            'index' => Pages\ListCodigoQrs::route('/'),
            'create' => Pages\CreateCodigoQr::route('/create'),
            'edit' => Pages\EditCodigoQr::route('/{record}/edit'),
        ];
    }
}
