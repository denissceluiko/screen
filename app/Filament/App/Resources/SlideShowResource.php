<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SlideShowResource\Pages;
use App\Filament\App\Resources\SlideShowResource\RelationManagers\SlidesRelationManager;
use App\Models\SlideShow;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SlideShowResource extends Resource
{
    protected static ?string $model = SlideShow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
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
            SlidesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlideShows::route('/'),
            'create' => Pages\CreateSlideShow::route('/create'),
            'edit' => Pages\EditSlideShow::route('/{record}/edit'),
        ];
    }
}
