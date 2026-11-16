<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An optional, plain-text subtitle shown under the title on the article
     * page, the poem page, and under each article section's heading.
     */
    public function up(): void
    {
        foreach (['articles', 'poems', 'article_sections'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('subtitle')->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        foreach (['articles', 'poems', 'article_sections'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropColumn('subtitle');
            });
        }
    }
};
