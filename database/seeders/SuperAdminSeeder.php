<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('auth.super_admin.email');
        $password = config('auth.super_admin.password');
        $name = config('auth.super_admin.name', 'Super Admin');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            $this->command?->warn('SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD not set — skipping super admin seed.');

            return;
        }

        $parts = preg_split('/\s+/', trim((string) $name), 2) ?: [];

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'first_name' => ($parts[0] ?? '') !== '' ? $parts[0] : 'Super',
                'last_name' => $parts[1] ?? 'Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );

        $role = Role::query()->where('name', 'super_admin')->first();

        if ($role === null) {
            $this->command?->warn('super_admin role missing — run AdminLteRbacSeeder first.');

            return;
        }

        $user->roles()->syncWithoutDetaching([$role->id]);
    }
}
