<?php

namespace App\Filament\App\Resources\SlideResource\Pages;

use App\Enums\SlideStatus;
use App\Enums\SlideTypes;
use App\Filament\App\Resources\SlideResource;
use App\Filament\Components\Schema\SlideFileUpload;
use App\Jobs\ProcessUploadedFile;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ListSlides extends ListRecords
{
    protected static string $resource = SlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk_upload')
                ->label(__('Bulk Upload'))
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    SlideFileUpload::make('files')
                        ->label(__('Files'))
                        ->multiple()
                        ->storeFileNamesIn('original_names'),
                ])
                ->action(function (array $data): void {
                    $files = (array) $data['files'];
                    $originalNames = (array) ($data['original_names'] ?? []);
                    $tenant = Filament::getTenant();

                    foreach ($files as $index => $filePath) {
                        $originalName = $originalNames[$index] ?? basename($filePath);
                        $name = pathinfo($originalName, PATHINFO_FILENAME);

                        $slide = $tenant->slides()->create([
                            'name' => $name,
                            'type' => SlideTypes::Image,
                            'original_path' => $filePath,
                            'original_name' => $originalName,
                            'token' => Str::random(32),
                            'status' => SlideStatus::Pending,
                        ]);

                        ProcessUploadedFile::dispatch($slide);
                    }

                    Notification::make()
                        ->title(trans_choice(':count slide queued|:count slides queued', count($files), ['count' => count($files)]))
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label(__('Preview'))
                    ->height(150)
                    ->getStateUsing(fn ($record) => route('slide.show', $record->token)),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
