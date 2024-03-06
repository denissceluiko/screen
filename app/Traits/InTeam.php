<?php

namespace App\Traits;

use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait InTeam
{
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function boot() {
        parent::boot();

        self::creating(function (Model $model) {
            if (empty($model->team_id)) {
                $model->team_id = Filament::getTenant()->id;
            }
        });
    }
}
