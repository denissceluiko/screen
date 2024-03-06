<?php

namespace App\Models;

use App\Traits\InTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlideShow extends Model
{
    use HasFactory, InTeam;

    protected $fillable = [
        'name', 'settings',
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
