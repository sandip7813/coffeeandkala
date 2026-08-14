<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst(fake()->unique()->words(2, true));

        return [
            'uuid' => (string) Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
            'type' => fake()->randomElement([Category::TYPE_FEATURE, Category::TYPE_JOURNAL]),
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 10),
            'status' => true,
        ];
    }
}
