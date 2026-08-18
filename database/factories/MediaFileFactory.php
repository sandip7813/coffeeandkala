<?php

namespace Database\Factories;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MediaFile>
 */
class MediaFileFactory extends Factory
{
    protected $model = MediaFile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = Str::random(20).'.jpg';

        return [
            'uuid' => (string) Str::uuid(),
            'type' => MediaFile::TYPE_GALLERY,
            'file_name' => $filename,
            'thumbnail_path' => 'gallery/thumbnails/'.$filename,
            'large_path' => 'gallery/large/'.$filename,
            'original_path' => 'gallery/'.$filename,
            'title' => ucfirst(fake()->words(3, true)),
            'caption' => fake()->sentence(),
            'status' => MediaFile::STATUS_ACTIVE,
            'uploaded_by' => User::factory(),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    public function ofType(string $type): static
    {
        return $this->state(fn () => [
            'type' => $type,
            'thumbnail_path' => "{$type}/thumbnails/".Str::random(20).'.jpg',
            'large_path' => "{$type}/large/".Str::random(20).'.jpg',
            'original_path' => "{$type}/".Str::random(20).'.jpg',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => MediaFile::STATUS_PENDING,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => MediaFile::STATUS_ACTIVE,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => MediaFile::STATUS_INACTIVE,
        ]);
    }
}
