<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SlideShowSlide extends Pivot
{
    #[\Override]
    protected $table = 'slide_slide_show';

    #[\Override]
    public $timestamps = true;
}
