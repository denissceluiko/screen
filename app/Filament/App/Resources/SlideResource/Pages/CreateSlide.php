<?php

namespace App\Filament\App\Resources\SlideResource\Pages;

use App\Enums\SlideStatus;
use App\Filament\App\Resources\SlideResource;
use App\Jobs\ProcessUploadedFile;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateSlide extends CreateRecord
{
    protected static string $resource = SlideResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['token'] = Str::random(32);
        $data['status'] = SlideStatus::Pending;

        $slide = Filament::getTenant()
            ->slides()
            ->create($data);

        ProcessUploadedFile::dispatch($slide);

        return $slide;
    }
}
