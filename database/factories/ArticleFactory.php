<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst(fake()->unique()->sentence(4));

        return [
            'uuid' => (string) Str::uuid(),
            'category_id' => Category::factory(),
            'type' => Article::TYPE_FEATURE,
            'title' => $title,
            'slug' => Str::slug($title),
            'introduction' => fake()->paragraph(),
            'editors_note' => fake()->paragraph(),
            'authors_note' => fake()->paragraph(),
            'status' => Article::STATUS_PENDING,
            'created_by' => User::factory(),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    public function feature(): self
    {
        return $this->state(fn (): array => [
            'type' => Article::TYPE_FEATURE,
            'category_id' => Category::factory()->state(['type' => Category::TYPE_FEATURE]),
        ]);
    }

    public function journal(): self
    {
        return $this->state(fn (): array => [
            'type' => Article::TYPE_JOURNAL,
            'category_id' => Category::factory()->state(['type' => Category::TYPE_JOURNAL]),
        ]);
    }

    public function active(): self
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Article::STATUS_ACTIVE,
            'approved_by' => $attributes['created_by'] ?? User::factory(),
            'approved_at' => now(),
        ]);
    }
}
