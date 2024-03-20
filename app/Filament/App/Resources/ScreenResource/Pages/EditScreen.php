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
            ]);
    }
}
