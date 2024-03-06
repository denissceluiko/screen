<?php

namespace App\Filament\App\Resources\SlideShowResource\Pages;

use App\Filament\App\Resources\SlideShowResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateSlideShow extends CreateRecord
{
    protected static string $resource = SlideShowResource::class;

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
