<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class OptimizerService
{
    public static function optimize(string $path): string
    {
        $disk = Storage::disk('slides');

        $manager = new ImageManager(new Driver());
        $image = $manager->read($disk->path($path));
        $image->save($disk->path($path));

        return $path;
    }
}
