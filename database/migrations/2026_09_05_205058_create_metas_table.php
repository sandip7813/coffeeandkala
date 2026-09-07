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
        Schema::create('metas', function (Blueprint $table) {
            $table->id();
            // Either a fixed, static frontend page (Home, Features index,
            // Journal index, Studio, Gallery, Poetry index, Our Story — see
            // App\Models\Meta::STATIC_PAGES) via page_key, OR a dynamic
            // record's own meta (a Category — a Feature/Journal category
            // page — or an Article — a Features/Journals content page) via
            // the polymorphic metable_* columns. Never both.
            $table->string('page_key')->nullable()->unique();
            $table->nullableMorphs('metable');
            $table->string('title')->nullable();
            $table->string('description', 500)->nullable();
            $table->string('keywords')->nullable();
            $table->timestamps();

            $table->unique(['metable_type', 'metable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metas');
    }
};
