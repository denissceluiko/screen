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
            ]);
    }
}
