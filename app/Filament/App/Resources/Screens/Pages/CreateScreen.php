<?php

namespace App\Filament\App\Resources\Screens\Pages;

use App\Filament\App\Resources\Screens\ScreenResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CreateScreen extends CreateRecord
{
    #[\Override]
    protected static string $resource = ScreenResource::class;

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    #[\Override]
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::random(32);

        return $data;
    }
}
