<?php

namespace App\Filament\App\Resources\Slides\Pages;

use App\Enums\SlideTypes;
use App\Filament\App\Resources\Slides\SlideResource;
use App\Filament\Components\Schema\SlideFileUpload;
use App\Services\OptimizerService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ListSlides extends ListRecords
{
    protected static string $resource = SlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulk_upload')
                ->label(__('Bulk Upload'))
                ->icon('heroicon-o-arrow-up-tray')
                ->schema([
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
                        $optimizedPath = OptimizerService::optimize($filePath);

                        $tenant->slides()->create([
                            'name' => $name,
                            'type' => SlideTypes::Image,
                            'path' => $optimizedPath,
                            'original_path' => $filePath,
                            'original_name' => $originalName,
                            'token' => Str::random(32),
                        ]);
                    }

                    Notification::make()
                        ->title(trans_choice(':count slide uploaded|:count slides uploaded', count($files), ['count' => count($files)]))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label(__('Preview'))
                    ->height(150)
                    ->getStateUsing(fn ($record) => route('slide.show', $record->token)),
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
