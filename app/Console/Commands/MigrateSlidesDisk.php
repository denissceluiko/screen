<?php

namespace App\Console\Commands;

use App\Models\Slide;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateSlidesDisk extends Command
{
    #[\Override]
    protected $signature = 'slides:migrate-disk';

    #[\Override]
    protected $description = 'Move existing slide files from the public disk to the private slides disk';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $slides = Storage::disk('slides');

        $records = Slide::all(['id', 'path', 'original_path']);

        if ($records->isEmpty()) {
            $this->info('No slides found, nothing to migrate.');

            return self::SUCCESS;
        }

        $this->info("Migrating {$records->count()} slide(s)...");

        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        $moved = 0;
        $skipped = 0;

        foreach ($records as $slide) {
            foreach (array_unique([$slide->path, $slide->original_path]) as $file) {
                if (blank($file) || $slides->exists($file)) {
                    continue;
                }

                if (! $public->exists($file)) {
                    $this->newLine();
                    $this->warn("Slide #{$slide->id}: file not found on public disk — {$file}");
                    $skipped++;

                    continue;
                }

                $slides->writeStream($file, $public->readStream($file));
                $moved++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Moved: {$moved}, Skipped/missing: {$skipped}");
        $this->line('Files on the public disk have been left in place. Remove them manually once you have verified the migration.');

        return self::SUCCESS;
    }
}
