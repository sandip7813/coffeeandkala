<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('image_position')->default('none');
            $table->string('content_position')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('youtube_position')->nullable();
            // What renders on the side opposite the video — kept distinct
            // from the section's own content/image, which stay a separate
            // image_position/content pairing rendered elsewhere in the
            // section.
            $table->text('video_companion_content')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_sections');
    }
};
