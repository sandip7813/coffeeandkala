<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Gallery/Studio captions are now a full description (textarea on the
     * admin form), not a one-line caption — varchar(255) was too short.
     * Raw MODIFY rather than Blueprint::change() (needs doctrine/dbal, not
     * installed) — additive/non-destructive, no data is dropped. MySQL-only:
     * tests run on in-memory SQLite (see phpunit.xml), which already treats
     * TEXT/VARCHAR the same for its purposes.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE media_files MODIFY caption TEXT NOT NULL');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE media_files MODIFY caption VARCHAR(255) NOT NULL');
    }
};
