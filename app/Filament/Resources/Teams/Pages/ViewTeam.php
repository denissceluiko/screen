<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewTeam extends ViewRecord
{
    #[\Override]
    protected static string $resource = TeamResource::class;

    #[\Override]
    public function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    #[\Override]
    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('slug'),
        ]);
    }
}
