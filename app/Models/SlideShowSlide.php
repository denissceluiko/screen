<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SlideShowSlide extends Pivot
{
    protected $table = 'slide_slide_show';

    public $timestamps = true;
}
