<?php

namespace App\Models;

use App\Traits\HasSettings;
use App\Traits\InTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Screen extends Model
{
    use HasFactory, InTeam, HasSettings;

    protected $fillable = [
        'name', 'slug', 'settings', 'slide_show_id',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static array $defaultSettings = [
        'width' => '1920',
        'height' => '1080',
        'updateInterval' => '10',
    ];

    public function slideShow(): BelongsTo
    {
        return $this->belongsTo(SlideShow::class);
    }
}
