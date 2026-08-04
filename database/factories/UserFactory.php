<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => null,
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
            'is_active' => true,
            'must_change_password' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Assign the given role name(s) after creating the user.
     *
     * @param  string|array<int, string>  $roles
     */
    public function withRoles(string|array $roles): static
    {
        $roles = (array) $roles;

        return $this->afterCreating(function (User $user) use ($roles): void {
            foreach ($roles as $role) {
                $user->assignRole($role);
            }
        });
    }

    /**
     * Create a super admin user (requires RBAC seeded roles).
     */
    public function superAdmin(): static
    {
        return $this->withRoles('super_admin');
    }

    /**
     * Create an editor user (requires RBAC seeded roles).
     */
    public function editor(): static
    {
        return $this->withRoles('editor');
    }

    /**
     * Create an admin user (requires RBAC seeded roles).
     */
    public function admin(): static
    {
        return $this->withRoles('admin');
    }
}
