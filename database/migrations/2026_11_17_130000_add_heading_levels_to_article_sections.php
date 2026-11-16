<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which HTML element each section's title and subtitle render as — 'p'
     * ("Normal"), 'h1'…'h4' (see ArticleSection::HEADING_LEVELS). Existing
     * sections keep today's look: an H2 title, and the subtitle as H3.
     */
    public function up(): void
    {
        Schema::table('article_sections', function (Blueprint $table): void {
            $table->string('title_level', 2)->default('h2')->after('title');
            $table->string('subtitle_level', 2)->default('h3')->after('subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('article_sections', function (Blueprint $table): void {
            $table->dropColumn(['title_level', 'subtitle_level']);
        });
    }
};
