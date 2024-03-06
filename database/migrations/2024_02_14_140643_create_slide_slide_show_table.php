<?php

use App\Models\Slide;
use App\Models\SlideShow;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slide_slide_show', function (Blueprint $table) {
            $table->foreignIdFor(SlideShow::class);
            $table->foreignIdFor(Slide::class);
            $table->timestamps();

            $table->primary(['slide_show_id', 'slide_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slide_slide_show');
    }
};
