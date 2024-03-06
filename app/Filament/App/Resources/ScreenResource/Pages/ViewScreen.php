<?php

namespace App\Filament\App\Resources\ScreenResource\Pages;

use App\Filament\App\Resources\ScreenResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewScreen extends ViewRecord
{
    protected static string $resource = ScreenResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Components\TextEntry::make('name'),
            Components\TextEntry::make('slug'),
        ]);
    }
}
