<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class Team extends Model
{
    use HasFactory;

    #[\Override]
    protected $fillable = [
        'name', 'slug',
    ];

    public static function current(): self
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof self) {
            throw new RuntimeException('No active Team tenant.');
        }

        return $tenant;
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function screens(): HasMany
    {
        return $this->hasMany(Screen::class);
    }

    public function slideShows(): HasMany
    {
        return $this->hasMany(SlideShow::class);
    }

    public function slides(): HasMany
    {
        return $this->hasMany(Slide::class);
    }
}
