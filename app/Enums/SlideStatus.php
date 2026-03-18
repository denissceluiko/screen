<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SlideStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Clean = 'clean';
    case Quarantined = 'quarantined';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Processing => __('Processing'),
            self::Clean => __('Clean'),
            self::Quarantined => __('Quarantined'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Processing => 'info',
            self::Clean => 'success',
            self::Quarantined => 'danger',
        };
    }
}
