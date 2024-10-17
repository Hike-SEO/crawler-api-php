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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->boolean('ignore_robots_txt')->default(false);
            $table->text('wait_until');
            $table->boolean('skip_ignored_paths');
            $table->unsignedInteger('page_timeout');
            $table->unsignedInteger('max_concurrent_pages');
            $table->boolean('hike_user_agent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
