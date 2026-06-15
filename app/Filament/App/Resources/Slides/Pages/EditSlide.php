<?php

namespace App\Filament\App\Resources\Slides\Pages;

use App\Filament\App\Resources\Slides\SlideResource;
use App\Services\OptimizerService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSlide extends EditRecord
{
    #[\Override]
    protected static string $resource = SlideResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[\Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['original_path'])) {
            $data['path'] = OptimizerService::optimize($data['original_path']);
        }

        return $data;
    }
}
