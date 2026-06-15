<?php

namespace App\Filament\App\Resources\Screens\Pages;

use App\Filament\App\Resources\Screens\ScreenResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewScreen extends ViewRecord
{
    #[\Override]
    protected static string $resource = ScreenResource::class;

    #[\Override]
    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('slug'),
        ]);
    }
}
