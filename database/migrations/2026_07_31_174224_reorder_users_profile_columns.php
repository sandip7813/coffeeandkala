<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'first_name')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->after('id')->change();
            $table->string('last_name')->nullable(false)->after('first_name')->change();
            $table->string('phone')->nullable()->after('email')->change();
        });
    }

    public function down(): void
    {
        // Column order is cosmetic; no reliable reverse needed.
    }
};
