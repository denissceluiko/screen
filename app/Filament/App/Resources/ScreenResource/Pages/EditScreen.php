<?php

namespace App\Filament\App\Resources\ScreenResource\Pages;

use App\Filament\App\Resources\ScreenResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditScreen extends EditRecord
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->disabled()
                    ->maxLength(60),
                Forms\Components\Select::make('slide_show_id')
                    ->label(__('Active slideshow'))
                    ->options(Filament::getTenant()
                        ->slideShows()
                        ->get()
                        ->pluck('name', 'id')
                    ),
                Forms\Components\Section::make('Settings')
                    ->description('Settings that apply to the screen and all its slideshows.')
                    ->schema([
                        Forms\Components\TextInput::make('settings.width')
                            ->integer()
                            ->suffix('px')
                            ->required(),
                        Forms\Components\TextInput::make('settings.height')
                            ->integer()
                            ->suffix('px')
                            ->required(),
                        Forms\Components\TextInput::make('settings.updateInterval')
                            ->helperText('How often the screen should receive updates?')
                            ->integer()
                            ->minValue(1)
                            ->suffix('seconds')
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
