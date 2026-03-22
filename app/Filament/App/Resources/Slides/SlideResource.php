<?php

namespace App\Filament\App\Resources\Slides;

use App\Enums\SlideTypes;
use App\Filament\App\Resources\Slides\Pages\CreateSlide;
use App\Filament\App\Resources\Slides\Pages\EditSlide;
use App\Filament\App\Resources\Slides\Pages\ListSlides;
use App\Filament\Components\Schema\SlideFileUpload;
use App\Models\Slide;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class SlideResource extends Resource
{
    protected static ?string $model = Slide::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),
                Select::make('type')
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
            'index' => ListSlides::route('/'),
            'create' => CreateSlide::route('/create'),
            'edit' => EditSlide::route('/{record}/edit'),
        ];
    }
}
