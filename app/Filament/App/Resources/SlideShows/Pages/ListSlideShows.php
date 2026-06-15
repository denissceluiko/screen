<?php

namespace App\Filament\App\Resources\SlideShows\Pages;

use App\Filament\App\Resources\SlideShows\SlideShowResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSlideShows extends ListRecords
{
    #[\Override]
    protected static string $resource = SlideShowResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
