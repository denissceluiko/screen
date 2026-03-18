<?php

use App\Enums\SlideStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->string('status')->default(SlideStatus::Pending->value)->after('token');
            $table->string('path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('path')->nullable(false)->change();
        });
    }
};
