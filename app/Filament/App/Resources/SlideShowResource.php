<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SlideShowResource\Pages;
use App\Filament\App\Resources\SlideShowResource\RelationManagers\SlidesRelationManager;
use App\Models\SlideShow;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlideShowResource extends Resource
{
    protected static ?string $model = SlideShow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slides_count')
                    ->counts('slides')
                    ->label(__('Slides'))
                    ->badge(),
                Tables\Columns\TextColumn::make('settings.switchInterval')
                    ->label(__('Switch Interval'))
                    ->getStateUsing(fn (SlideShow $record): string => $record->settings['switchInterval'] ?? '5')
                    ->suffix(__('s')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('duplicate')
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SlidesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlideShows::route('/'),
            'create' => Pages\CreateSlideShow::route('/create'),
            'edit' => Pages\EditSlideShow::route('/{record}/edit'),
        ];
    }
}
