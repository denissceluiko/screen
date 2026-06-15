<?php

namespace App\Filament\App\Resources\SlideShows\Pages;

use App\Filament\App\Resources\SlideShows\SlideShowResource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditSlideShow extends EditRecord
{
    #[\Override]
    protected static string $resource = SlideShowResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('settings.switchInterval')
                    ->helperText('Amount of seconds one slide remains on screen')
                    ->integer()
                    ->minValue(1)
                    ->suffix('seconds')
                    ->required(),
            ]);
    }
}
