<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasSettings
{
    public function settings(): Attribute
    {
        return Attribute::make(
            get: function(?string $value): array {
                try {
                    $decoded = empty($value) ? [] : json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    $decoded = [];
                }
                return array_merge(static::$defaultSettings, $decoded ?? []);
            },
            set: fn(array $value) => json_encode($value),
        );
    }
}
