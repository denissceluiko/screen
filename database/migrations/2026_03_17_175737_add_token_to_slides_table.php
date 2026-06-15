<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->string('token', 32)->nullable()->unique()->after('id');
        });

        foreach (DB::table('slides')->get() as $slide) {
            DB::table('slides')->where('id', $slide->id)->update(['token' => Str::random(32)]);
        }

        Schema::table('slides', function (Blueprint $table) {
            $table->string('token', 32)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
