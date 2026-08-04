<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'name')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->default('')->after('id');
            }

            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->default('')->after('first_name');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
        });

        foreach (DB::table('users')->orderBy('id')->cursor() as $user) {
            $parts = preg_split('/\s+/', trim((string) $user->name), 2) ?: [];

            DB::table('users')->where('id', $user->id)->update([
                'first_name' => ($parts[0] ?? '') !== '' ? $parts[0] : 'User',
                'last_name' => $parts[1] ?? '',
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'name') || ! Schema::hasColumn('users', 'first_name')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->default('');
        });

        foreach (DB::table('users')->orderBy('id')->cursor() as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'name' => trim($user->first_name.' '.$user->last_name),
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'phone']);
        });
    }
};
