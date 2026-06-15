<?php

namespace App\Models;

use App\Traits\InTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** @property SlideShowSlide $pivot */
class Slide extends Model
{
    use HasFactory, InTeam;

    #[\Override]
    protected $fillable = [
        'name', 'type', 'path', 'original_path', 'original_name', 'token',
    ];

    public function slideShows(): BelongsToMany
    {
        return $this->belongsToMany(SlideShow::class);
    }
}
