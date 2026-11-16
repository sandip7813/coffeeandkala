<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slug_redirects', function (Blueprint $table): void {
            $table->id();
            // The record (Article/Poem) this old slug now redirects to — its
            // current URL is always built from its live slug, so a slug
            // changed several times never produces a redirect chain.
            $table->morphs('redirectable');
            // For articles, the category the old URL lived under (an
            // article's URL is /{type}/{category}/{slug}, and slugs are only
            // unique per category). Null for poems.
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->timestamps();

            $table->index(['redirectable_type', 'category_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slug_redirects');
    }
};
