<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminLteRbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function seedRbac(): void
{
    test()->seed(AdminLteRbacSeeder::class);
}

/**
 * Create a user in a fresh, otherwise-empty role holding exactly the given
 * permission(s) — for asserting a granular permission works on its own,
 * without any of the other permissions a seeded role might bundle it with.
 *
 * @param  string|array<int, string>  $permissions
 */
function userWithPermission(string|array $permissions): User
{
    // Every admin route sits behind 'permission:view-dashboard' at the route
    // group level, so it's implied here rather than repeated at every call site.
    $permissions = array_unique([...(array) $permissions, 'view-dashboard']);

    $role = Role::create(['name' => 'test-'.Str::random(8)]);
    $role->permissions()->sync(
        Permission::whereIn('name', $permissions)->pluck('id')
    );

    $user = User::factory()->create();
    $user->roles()->attach($role);

    return $user;
}

/**
 * @return array<string, int>
 */
function unlockedArtisanSession(): array
{
    return ['artisan_runner.confirmed_at' => time()];
}
