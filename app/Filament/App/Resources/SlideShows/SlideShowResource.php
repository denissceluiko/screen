<?php

namespace App\Filament\App\Resources\SlideShows;

use App\Filament\App\Resources\SlideShows\Pages\CreateSlideShow;
use App\Filament\App\Resources\SlideShows\Pages\EditSlideShow;
use App\Filament\App\Resources\SlideShows\Pages\ListSlideShows;
use App\Filament\App\Resources\SlideShows\RelationManagers\SlidesRelationManager;
use App\Models\SlideShow;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SlideShowResource extends Resource
{
    #[\Override]
    protected static ?string $model = SlideShow::class;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slides_count')
                    ->counts('slides')
                    ->label(__('Slides'))
                    ->badge(),
                TextColumn::make('settings.switchInterval')
                    ->label(__('Switch Interval'))
                    ->getStateUsing(fn (SlideShow $record): string => $record->settings['switchInterval'] ?? '5')
                    ->suffix(__('s')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (SlideShow $record): void {
                        $copy = $record->replicate(['id', 'created_at', 'updated_at', 'slides_count']);
                        $copy->name = __('Copy of :name', ['name' => $record->name]);
                        $copy->save();

                        foreach ($record->slides as $slide) {
                            $copy->slides()->attach($slide->id, [
                                'sort_order' => $slide->pivot->sort_order,
                            ]);
                        }

                        Notification::make()
                            ->title(__('Slideshow duplicated'))
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            SlidesRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListSlideShows::route('/'),
            'create' => CreateSlideShow::route('/create'),
            'edit' => EditSlideShow::route('/{record}/edit'),
        ];
    }
}
