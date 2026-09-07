<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reverts the "nothing is mandatory" experiment on the Essentials tab:
     * only the Content tab (sections/faqs) stays fully optional. Uses raw
     * MODIFY statements rather than Blueprint::change() (which needs
     * doctrine/dbal, not installed) — additive/non-destructive, no data is
     * dropped. MySQL-only: the app's tests run on an in-memory SQLite
     * connection (see phpunit.xml) that doesn't support MODIFY COLUMN and
     * doesn't need the DB-level constraint — FormRequest validation is
     * what the tests exercise.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('articles', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
        });

        DB::statement('ALTER TABLE articles MODIFY category_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE articles MODIFY title VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE articles MODIFY slug VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE articles MODIFY introduction TEXT NOT NULL');
        DB::statement('ALTER TABLE articles MODIFY editors_note TEXT NOT NULL');
        DB::statement('ALTER TABLE articles MODIFY authors_note TEXT NOT NULL');

        Schema::table('articles', function (Blueprint $table): void {
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('articles', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
        });

        DB::statement('ALTER TABLE articles MODIFY category_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE articles MODIFY title VARCHAR(255) NULL');
        DB::statement('ALTER TABLE articles MODIFY slug VARCHAR(255) NULL');
        DB::statement('ALTER TABLE articles MODIFY introduction TEXT NULL');
        DB::statement('ALTER TABLE articles MODIFY editors_note TEXT NULL');
        DB::statement('ALTER TABLE articles MODIFY authors_note TEXT NULL');

        Schema::table('articles', function (Blueprint $table): void {
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }
};
