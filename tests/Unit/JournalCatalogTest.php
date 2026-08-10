<?php

use App\Support\JournalCatalog;

test('category highlights returns exactly one entry per named category', function () {
    $expectedCategoryIds = collect(JournalCatalog::all())
        ->pluck('category_id')
        ->filter()
        ->unique()
        ->values();

    $highlights = collect(JournalCatalog::categoryHighlights());

    expect($highlights)->toHaveCount($expectedCategoryIds->count());
    expect($highlights->pluck('category_id')->sort()->values()->all())
        ->toBe($expectedCategoryIds->sort()->values()->all());
});

test('category highlights never repeats a category', function () {
    $highlights = collect(JournalCatalog::categoryHighlights());

    expect($highlights->pluck('category_id')->unique())->toHaveCount($highlights->count());
});

test('category highlights picks the newest entry when a category has more than one', function () {
    $highlights = collect(JournalCatalog::categoryHighlights())->keyBy('category_id');
    $entries = collect(JournalCatalog::all())->filter(fn (array $entry) => ! empty($entry['category_id']));

    foreach ($highlights as $categoryId => $highlight) {
        $newestForCategory = $entries->where('category_id', $categoryId)->sortByDesc('date')->first();

        expect($highlight['title'])->toBe($newestForCategory['title']);
    }
});
