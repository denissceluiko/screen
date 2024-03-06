<?php

namespace App\Filament\App\Resources\ScreenResource\Pages;

use App\Filament\App\Resources\ScreenResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateScreen extends CreateRecord
{
    protected static string $resource = ScreenResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = substr(sha1(uniqid()), 0, 15);

        return $data;
    }
}
