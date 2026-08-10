<?php

use App\Support\FeatureCatalog;

test('the edition gives the main section exactly one article per category', function () {
    $entries = collect(FeatureCatalog::edition());
    $main = $entries->whereIn('role', ['lead', 'feature']);

    expect($main)->toHaveCount(count(FeatureCatalog::all()));
    expect($main->pluck('category_id')->unique())->toHaveCount($main->count());
    expect($main->pluck('category_id')->sort()->values()->all())
        ->toBe(collect(FeatureCatalog::slugs())->sort()->values()->all());
});

test('the edition places not-on-the-atlas in the main section, not the sidebar', function () {
    $entries = collect(FeatureCatalog::edition());

    $notOnTheAtlas = $entries->firstWhere('category_id', 'not-on-the-atlas');

    expect($notOnTheAtlas)->not->toBeNull();
    expect($notOnTheAtlas['role'])->toBeIn(['lead', 'feature']);
});

test('the sidebar only holds articles left over from the main section, newest first', function () {
    $entries = collect(FeatureCatalog::edition());
    $mainTitles = $entries->whereIn('role', ['lead', 'feature'])->pluck('title');
    $sidebar = $entries->whereIn('role', ['column', 'brief', 'spotlight']);

    expect($sidebar->pluck('title')->intersect($mainTitles))->toBeEmpty();
    expect($sidebar->pluck('date')->all())
        ->toBe($sidebar->pluck('date')->sortDesc()->values()->all());
});

test('the sidebar closes with a real leftover article, not static copy', function () {
    $entries = collect(FeatureCatalog::edition());
    $spotlight = $entries->firstWhere('role', 'spotlight');

    expect($spotlight)->not->toBeNull();
    expect($spotlight['title'])->not->toBe('The best stories arrive without a map.');
    expect($spotlight)->toHaveKeys(['title', 'excerpt', 'href', 'category_id', 'category_name', 'date']);
});
