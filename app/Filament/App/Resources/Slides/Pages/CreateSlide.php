<?php

namespace App\Filament\App\Resources\Slides\Pages;

use App\Filament\App\Resources\Slides\SlideResource;
use App\Models\Team;
use App\Services\OptimizerService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateSlide extends CreateRecord
{
    #[\Override]
    protected static string $resource = SlideResource::class;

    #[\Override]
    protected function handleRecordCreation(array $data): Model
    {
        $data['path'] = OptimizerService::optimize($data['original_path']);
        $data['token'] = Str::random(32);

        return Team::current()
            ->slides()
            ->create($data);
    }
}
