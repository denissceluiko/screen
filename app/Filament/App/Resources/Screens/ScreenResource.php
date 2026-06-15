<?php

namespace App\Filament\App\Resources\Screens;

use App\Filament\App\Resources\Screens\Pages\CreateScreen;
use App\Filament\App\Resources\Screens\Pages\EditScreen;
use App\Filament\App\Resources\Screens\Pages\ListScreens;
use App\Filament\App\Resources\Screens\Pages\ViewScreen;
use App\Models\Screen;
use Filament\Resources\Resource;

class ScreenResource extends Resource
{
    #[\Override]
    protected static ?string $model = Screen::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListScreens::route('/'),
            'create' => CreateScreen::route('/create'),
            'view' => ViewScreen::route('/{record}'),
            'edit' => EditScreen::route('/{record}/edit'),
        ];
    }
}
