<?php

namespace App\Filament\App\Resources\SlideShows\Pages;

use App\Filament\App\Resources\SlideShows\SlideShowResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateSlideShow extends CreateRecord
{
    protected static string $resource = SlideShowResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),
            ]);
    }
}
