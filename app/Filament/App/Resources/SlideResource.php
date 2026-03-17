<?php

namespace App\Filament\App\Resources;

use App\Enums\SlideTypes;
use App\Filament\App\Resources\SlideResource\Pages;
use App\Filament\Components\Schema\SlideFileUpload;
use App\Models\Slide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class SlideResource extends Resource
{
    protected static ?string $model = Slide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(SlideTypes::class)
                    ->default(SlideTypes::Image)
                    ->required(),
                SlideFileUpload::make()
                    ->storeFileNamesIn('original_name')
                    ->label(__('File')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlides::route('/'),
            'create' => Pages\CreateSlide::route('/create'),
            'edit' => Pages\EditSlide::route('/{record}/edit'),
        ];
    }
}
