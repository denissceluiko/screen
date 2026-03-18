<?php

namespace App\Filament\App\Resources\SlideResource\Pages;

use App\Enums\SlideStatus;
use App\Filament\App\Resources\SlideResource;
use App\Jobs\ProcessUploadedFile;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSlide extends EditRecord
{
    protected static string $resource = SlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['original_path'])) {
            $data['path'] = null;
            $data['status'] = SlideStatus::Pending;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        if ($record->status === SlideStatus::Pending) {
            ProcessUploadedFile::dispatch($record);
        }
    }
}
