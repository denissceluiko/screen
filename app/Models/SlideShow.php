<?php

namespace App\Models;

use App\Traits\HasSettings;
use App\Traits\InTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlideShow extends Model
{
    use HasFactory, HasSettings, InTeam;

    #[\Override]
    protected $fillable = [
        'name', 'settings',
    ];

    #[\Override]
    protected $casts = [
        'settings' => 'array',
    ];

    protected static array $defaultSettings = [
        'switchInterval' => '5',
    ];

    /** @return BelongsToMany<Slide, $this, SlideShowSlide, 'pivot'> */
    public function slides(): BelongsToMany
    {
        return $this->belongsToMany(Slide::class)
            ->using(SlideShowSlide::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order', 'asc');
    }

    public function screen(): HasMany
    {
        return $this->hasMany(Screen::class);
    }
}
