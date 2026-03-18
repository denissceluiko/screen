<?php

namespace App\Jobs;

use App\Contracts\FileScanner;
use App\Enums\SlideStatus;
use App\Models\Slide;
use App\Services\OptimizerService;
use App\Services\Scanners\ClamAvScanner;
use App\Services\Scanners\VirusTotalScanner;
use App\Services\Scanners\YaraScanner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessUploadedFile implements ShouldQueue
{
    use Queueable;

    /** @var class-string<FileScanner>[] */
    protected array $scanners = [
        YaraScanner::class,
        VirusTotalScanner::class,
        ClamAvScanner::class,
    ];

    public function __construct(public readonly Slide $slide) {}

    public function handle(): void
    {
        $this->slide->update(['status' => SlideStatus::Processing]);

        $absolutePath = Storage::disk('slides')->path($this->slide->original_path);

        foreach ($this->scanners as $scannerClass) {
            /** @var FileScanner $scanner */
            $scanner = app($scannerClass);
            $result = $scanner->scan($absolutePath);

            if (! $result->passed) {
                $this->slide->update(['status' => SlideStatus::Quarantined]);

                return;
            }
        }

        $optimizedPath = OptimizerService::optimize($this->slide->original_path);

        $this->slide->update([
            'path' => $optimizedPath,
            'status' => SlideStatus::Clean,
        ]);
    }
}
