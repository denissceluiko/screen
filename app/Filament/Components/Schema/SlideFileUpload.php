<?php

namespace App\Filament\Components\Schema;

use Filament\Forms\Components\FileUpload;

class SlideFileUpload
{
    public static function make(string $fieldName = 'original_path'): FileUpload
    {
        return FileUpload::make($fieldName)
            ->disk('slides')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->maxSize(10240)
            ->rules(['extensions:jpg,jpeg,png,gif,webp'])
            ->required();
    }
}
