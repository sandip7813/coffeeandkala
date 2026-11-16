<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Both slug columns are already NOT NULL; this also rejects an empty
     * (or whitespace-only) slug at the database level, as a backstop to
     * App\Models\Concerns\HasSlug, which always fills a blank slug before
     * saving. MySQL/MariaDB only, like the other raw-SQL migrations — the
     * tests' in-memory SQLite connection can't add a CHECK to an existing
     * table, and the tests exercise HasSlug itself.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE articles ADD CONSTRAINT articles_slug_not_empty CHECK (TRIM(slug) <> '')");
        DB::statement("ALTER TABLE poems ADD CONSTRAINT poems_slug_not_empty CHECK (TRIM(slug) <> '')");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE articles DROP CONSTRAINT articles_slug_not_empty');
        DB::statement('ALTER TABLE poems DROP CONSTRAINT poems_slug_not_empty');
    }
};
