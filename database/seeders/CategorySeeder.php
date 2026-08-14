<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'feature' => [
                'Art & Culture',
                'Experiences',
                'On A Budget',
                'Luxury Escapes',
                'Global Chapters',
                'Not On The Atlas',
                'Vineyard Tales',
                'Coffee & Classics',
            ],
            'journal' => [
                'The Bigger Picture',
                'Worth Knowing',
                'Chapters Over Coffee',
            ],
        ];

        foreach ($categories as $type => $titles) {
            foreach ($titles as $index => $title) {
                Category::query()->firstOrCreate(
                    ['type' => $type, 'slug' => Str::slug($title)],
                    [
                        'uuid' => (string) Str::uuid(),
                        'title' => $title,
                        'sort_order' => $index,
                        'status' => true,
                    ],
                );
            }
        }
    }
}
