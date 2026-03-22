<?php

namespace App\Traits;

use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

trait InTeam
{
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function (Model $model) {
            if (empty($model->team_id)) {
                $tenant = Filament::getTenant();

                throw_if(is_null($tenant), RuntimeException::class,
                    'Cannot create '.class_basename($model).' without an active tenant context.'
                );

                $model->team_id = $tenant->id;
            }
        });
    }
}
