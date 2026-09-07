<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A section now picks ONE media type — image or YouTube video — rather
     * than carrying both an image and a video independently. The video's
     * companion side reuses the section's own `content` field when set to
     * "text" instead of duplicating it in a separate column.
     */
    public function up(): void
    {
        Schema::table('article_sections', function (Blueprint $table): void {
            $table->string('media_type')->nullable()->after('title');
            $table->string('video_companion_type')->nullable()->after('youtube_position');
            $table->dropColumn('video_companion_content');
        });
    }

    public function down(): void
    {
        Schema::table('article_sections', function (Blueprint $table): void {
            $table->dropColumn(['media_type', 'video_companion_type']);
            $table->text('video_companion_content')->nullable();
        });
    }
};
