<?php

namespace App\Filament\App\Resources\Screens\Pages;

use App\Filament\App\Resources\Screens\ScreenResource;
use App\Models\Screen;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListScreens extends ListRecords
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->searchable(),
            TextColumn::make('slug')
                ->label(__('Display URL'))
                ->getStateUsing(fn (Screen $record): string => route('screen.display', $record))
                ->copyable()
                ->copyMessage(__('URL copied'))
                ->color('gray')
                ->fontFamily('mono'),
            TextColumn::make('slideShow.name')
                ->placeholder(__('None'))
                ->searchable(),
            TextColumn::make('last_seen_at')
                ->label(__('Last Seen'))
                ->since()
                ->placeholder(__('Never'))
                ->sortable(),
        ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('Open')
                    ->url(fn (Screen $record): string => route('screen.display', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
