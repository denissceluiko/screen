<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SlideTypes: string implements HasLabel
{
    case Image = 'image';
    // case Video = 'video';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
