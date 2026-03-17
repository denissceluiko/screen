<?php

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
        Schema::table('slide_slide_show', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')->default(0)->after('slide_id');
        });
    }

    public function down(): void
    {
        Schema::table('slide_slide_show', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
