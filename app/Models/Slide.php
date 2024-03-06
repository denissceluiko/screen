<?php

namespace App\Models;

use App\Traits\InTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Slide extends Model
{
    use HasFactory, InTeam;

    protected $fillable = [
        'name', 'type', 'path', 'original_path', 'original_name',
    ];

    public function slideShows(): BelongsToMany
    {
        return $this->belongsToMany(SlideShow::class);
    }
}
