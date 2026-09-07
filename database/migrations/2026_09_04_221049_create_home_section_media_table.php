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
        Schema::create('home_section_media', function (Blueprint $table) {
            $table->id();
            // 'gallery' | 'studio' — see App\Models\HomeSectionMedia::SECTIONS.
            $table->string('section');
            $table->foreignId('media_id')->constrained('media_files')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['section', 'media_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_section_media');
    }
};
