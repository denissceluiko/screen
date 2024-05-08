<?php

namespace App\Filament\App\Resources\SlideShowResource\Pages;

use App\Filament\App\Resources\SlideShowResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditSlideShow extends EditRecord
{
    protected static string $resource = SlideShowResource::class;

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
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('settings.switchInterval')
                    ->helperText('Amount of seconds one slide remains on screen')
                    ->integer()
                    ->minValue(1)
                    ->suffix('seconds')
                    ->required(),
            ]);
    }
}
