<?php

namespace App\Models;

use App\Traits\InTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Screen extends Model
{
    use HasFactory, InTeam;

    protected $fillable = [
        'name', 'slug', 'settings', 'slide_show_id',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function slideShow(): BelongsTo
    {
        return $this->belongsTo(SlideShow::class);
    }
}
