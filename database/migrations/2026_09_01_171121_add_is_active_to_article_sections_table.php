<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Only inactive sections are hidden — the article itself keeps its own
     * pending/active/inactive status independently. Defaults to true so
     * every existing section (and any created on the Add-article form,
     * which doesn't expose this toggle) starts visible.
     */
    public function up(): void
    {
        Schema::table('article_sections', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('media_type');
        });
    }

    public function down(): void
    {
        Schema::table('article_sections', function (Blueprint $table): void {
            $table->dropColumn('is_active');
        });
    }
};
