<?php

namespace App\Filament\App\Resources\SlideShows\RelationManagers;

use App\Filament\App\Resources\Slides\SlideResource;
use App\Models\Slide;
use App\Models\SlideShow;
use App\Models\Team;
use App\Services\OptimizerService;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use RuntimeException;

class SlidesRelationManager extends RelationManager
{
    #[\Override]
    protected static string $relationship = 'slides';

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return SlideResource::form($schema);
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
            ->headerActions([
                AttachAction::make()
                    ->recordSelectOptionsQuery(fn () => Team::current()->slides())
                    ->after(function (Slide $record): void {
                        $slideshow = $this->ownerRecord();
                        $maxOrder = $slideshow->slides()
                            ->where('slides.id', '!=', $record->id)
                            ->max('slide_slide_show.sort_order') ?? 0;
                        $slideshow->slides()->updateExistingPivot($record->id, [
                            'sort_order' => $maxOrder + 1,
                        ]);
                    }),
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['path'] = OptimizerService::optimize($data['original_path']);
                        $data['token'] = Str::random(32);

                        return $data;
                    })
                    ->after(function (Slide $record): void {
                        $slideshow = $this->ownerRecord();
                        $maxOrder = $slideshow->slides()
                            ->where('slides.id', '!=', $record->id)
                            ->max('slide_slide_show.sort_order') ?? 0;
                        $slideshow->slides()->updateExistingPivot($record->id, [
                            'sort_order' => $maxOrder + 1,
                        ]);
                    }),
            ])
            ->recordActions([
                DetachAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function reorderTable(array $order, string|int|null $draggedRecordKey = null): void
    {
        $slideshow = $this->ownerRecord();

        foreach ($order as $position => $slideId) {
            $slideshow->slides()->updateExistingPivot($slideId, [
                'sort_order' => $position + 1,
            ]);
        }
    }

    private function ownerRecord(): SlideShow
    {
        $record = $this->getOwnerRecord();

        if (! $record instanceof SlideShow) {
            throw new RuntimeException('Expected SlideShow owner record.');
        }

        return $record;
    }
}
