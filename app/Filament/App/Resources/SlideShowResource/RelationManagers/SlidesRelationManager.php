<?php

namespace App\Filament\App\Resources\SlideShowResource\RelationManagers;

use App\Filament\App\Resources\SlideResource;
use App\Services\OptimizerService;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SlidesRelationManager extends RelationManager
{
    protected static string $relationship = 'slides';

    public function form(Form $form): Form
    {
        return SlideResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label(__('Preview'))
                    ->height(150),
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(fn () => Filament::getTenant()->slides()),
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['path'] = OptimizerService::optimize($data['original_path']);
                        
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
