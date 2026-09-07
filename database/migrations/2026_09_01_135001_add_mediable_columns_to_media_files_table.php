<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Turns media_files into a general media store: Gallery/Studio uploads
     * keep using it exactly as before (mediable_* stay null for them), while
     * Article featured images / section images / section galleries attach to
     * their owning Article or ArticleSection via the new polymorphic columns.
     */
    public function up(): void
    {
        Schema::table('media_files', function (Blueprint $table): void {
            $table->nullableMorphs('mediable');
            $table->string('role')->nullable()->after('mediable_type');
            $table->unsignedInteger('sort_order')->default(0)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('media_files', function (Blueprint $table): void {
            $table->dropMorphs('mediable');
            $table->dropColumn(['role', 'sort_order']);
        });
    }
};
