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
    use HasFactory, InTeam, HasSettings;

    protected $fillable = [
        'name', 'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static array $defaultSettings = [
        'switchInterval' => '5',
    ];

    public function slides(): BelongsToMany
    {
        return $this->belongsToMany(Slide::class);
    }

    public function screen(): HasMany
    {
        return $this->hasMany(Screen::class);
    }
}
