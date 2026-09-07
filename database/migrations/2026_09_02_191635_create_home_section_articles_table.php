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
        Schema::create('home_section_articles', function (Blueprint $table) {
            $table->id();
            // 'latest_pieces' | 'the_selection' | 'features' | 'journal' —
            // see App\Models\HomeSectionArticle::SECTIONS.
            $table->string('section');
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['section', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_section_articles');
    }
};
