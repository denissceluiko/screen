<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ScreenResource\Pages;
use App\Filament\App\Resources\ScreenResource\RelationManagers;
use App\Models\Screen;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScreenResource extends Resource
{
    protected static ?string $model = Screen::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScreens::route('/'),
            'create' => Pages\CreateScreen::route('/create'),
            'view' => Pages\ViewScreen::route('/{record}'),
            'edit' => Pages\EditScreen::route('/{record}/edit'),
        ];
    }
}
