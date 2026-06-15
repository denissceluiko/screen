<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateTeam extends CreateRecord
{
    #[\Override]
    protected static string $resource = TeamResource::class;

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }

    #[\Override]
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = substr(md5($data['name'].time()), 0, 6);

        return $data;
    }
}
