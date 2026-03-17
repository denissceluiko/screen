<?php

namespace App\Filament\App\Resources\SlideShowResource\RelationManagers;

use App\Filament\App\Resources\SlideResource;
use App\Models\Slide;
use App\Services\OptimizerService;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->addSelect('slides.*')
                ->addSelect('slide_slide_show.sort_order as sort_order')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label(__('Preview'))
                    ->height(150)
                    ->getStateUsing(fn ($record) => route('slide.show', $record->token)),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(fn () => Filament::getTenant()->slides())
                    ->after(function (Slide $record) {
                        $slideshow = $this->getOwnerRecord();
                        $maxOrder = $slideshow->slides()
                            ->where('slides.id', '!=', $record->id)
                            ->max('slide_slide_show.sort_order') ?? 0;
                        $slideshow->slides()->updateExistingPivot($record->id, [
                            'sort_order' => $maxOrder + 1,
                        ]);
                    }),
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['path'] = OptimizerService::optimize($data['original_path']);
                        $data['token'] = Str::random(32);

                        return $data;
                    })
                    ->after(function (Slide $record) {
                        $slideshow = $this->getOwnerRecord();
                        $maxOrder = $slideshow->slides()
                            ->where('slides.id', '!=', $record->id)
                            ->max('slide_slide_show.sort_order') ?? 0;
                        $slideshow->slides()->updateExistingPivot($record->id, [
                            'sort_order' => $maxOrder + 1,
                        ]);
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

    public function reorderTable(array $order): void
    {
        $slideshow = $this->getOwnerRecord();

        foreach ($order as $position => $slideId) {
            $slideshow->slides()->updateExistingPivot($slideId, [
                'sort_order' => $position + 1,
            ]);
        }
    }
}
