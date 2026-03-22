<?php

namespace App\Filament\App\Resources\Screens\Pages;

use App\Filament\App\Resources\Screens\ScreenResource;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditScreen extends EditRecord
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

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
                    ->options(Filament::getTenant()
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
