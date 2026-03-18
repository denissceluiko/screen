<?php

namespace App\Models;

use App\Enums\SlideStatus;
use App\Traits\InTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Slide extends Model
{
    use HasFactory, InTeam;

    protected $fillable = [
        'name', 'type', 'path', 'original_path', 'original_name', 'token', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => SlideStatus::class,
        ];
    }

    public function slideShows(): BelongsToMany
    {
        return $this->belongsToMany(SlideShow::class);
    }
}
