<?php

namespace App\Filament\App\Resources\ScreenResource\Pages;

use App\Filament\App\Resources\ScreenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns;
use Filament\Tables\Table;

class ListScreens extends ListRecords
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Columns\TextColumn::make('name'),
            Columns\TextColumn::make('slug'),
            Columns\TextColumn::make('slideShow.name')
                ->placeholder(__('None')),
        ])->filters([
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
