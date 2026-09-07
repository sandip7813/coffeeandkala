<?php

namespace Database\Factories;

use App\Models\Poem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Poem>
 */
class PoemFactory extends Factory
{
    protected $model = Poem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst(fake()->unique()->sentence(3));

        return [
            'uuid' => (string) Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
            'body' => implode("\n", fake()->sentences(6)),
            'status' => Poem::STATUS_PENDING,
            'created_by' => User::factory(),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Poem::STATUS_ACTIVE,
            'approved_by' => $attributes['created_by'] ?? User::factory(),
            'approved_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'status' => Poem::STATUS_PENDING,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => Poem::STATUS_INACTIVE,
        ]);
    }
}
