<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function show(Request $request, Slide $slide): \Symfony\Component\HttpFoundation\Response
    {
        $disk = Storage::disk('slides');

        abort_unless($disk->exists($slide->path), 404);

        $etag = '"' . md5($slide->token . $slide->updated_at->timestamp) . '"';

        if ($request->header('If-None-Match') === $etag) {
            return response()->noContent(304);
        }

        return $disk->response($slide->path, null, [
            'Cache-Control' => 'public, max-age=86400',
            'ETag' => $etag,
            'Last-Modified' => $slide->updated_at->toRfc7231String(),
        ]);
    }
}