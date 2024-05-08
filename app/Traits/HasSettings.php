<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasSettings
{
    public function settings(): Attribute
    {
        return Attribute::make(
            get: function(?string $value): array {
                $value = empty($value) ? [] : json_decode($value, true);
                $value ??= [];
                return array_merge(static::$defaultSettings, $value);
            },
            set: fn(array $value) => json_encode($value),
        );
    }
}
