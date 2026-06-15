<?php

namespace App\Filament\App\Resources\Screens\Pages;

use App\Filament\App\Resources\Screens\ScreenResource;
use App\Models\Team;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditScreen extends EditRecord
{
    #[\Override]
    protected static string $resource = ScreenResource::class;

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
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->disabled()
                    ->maxLength(60),
                Select::make('slide_show_id')
                    ->label(__('Active slideshow'))
                    ->options(Team::current()
                        ->slideShows()
                        ->get()
                        ->pluck('name', 'id')
                    ),
                Section::make('Settings')
                    ->description('Settings that apply to the screen and all its slideshows.')
                    ->schema([
                        TextInput::make('settings.width')
                            ->integer()
                            ->suffix('px')
                            ->required(),
                        TextInput::make('settings.height')
                            ->integer()
                            ->suffix('px')
                            ->required(),
                        TextInput::make('settings.updateInterval')
                            ->helperText('How often the screen should receive updates?')
                            ->integer()
                            ->minValue(1)
                            ->suffix('seconds')
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
