<?php

namespace App\Filament\App\Resources\SlideResource\Pages;

use App\Filament\App\Resources\SlideResource;
use App\Services\OptimizerService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSlide extends CreateRecord
{
    protected static string $resource = SlideResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['path'] = OptimizerService::optimize($data['original_path']);

        return Filament::getTenant()
            ->slides()
            ->create($data);
    }
}
